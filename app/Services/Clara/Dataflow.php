<?php

namespace App\Services\Clara;

use Excel;
use XMLWriter;
use Facades\App\Repositories\DataflowRepository;

class Dataflow
{    
    protected $aIds = [];
    
    protected $oFile = null;

    public static function getRepositories()
    {
        $aRepositories = [];
        
        $aListRepositories = glob(app_path('Repositories\*'));
        foreach ($aListRepositories as $sRepository)
        {
            $sRepository = substr(strrchr($sRepository, '\\'), 1);
            $sRepository = strstr($sRepository, '.php', true);
            
            $aRepositories['App\Repositories\\'.$sRepository] = $sRepository;
        }
        
        return $aRepositories;
    }
    
    public function setIds(array $aIds)
    {
        $this->aIds = $aIds;
    }
    
    private function setLimit()
    {
        ini_set('memory_limit','3g');
        set_time_limit(0);
    }
    
    public function flow($sFormat, $sToken, $sParam)
    {
        $this->setLimit();
        $this->setFlow($sToken);
        $this->createFile($sFormat);
        
        switch ($sParam)
        {
            case 'cache':
                return $this->getCache($sFormat);
                break;
            
            case 'download':
                return $this->download($sFormat);
                break;
            
            default:
                return $this->createFlow($sFormat);
                break;
        }
    }
    
    private function createFile($sFormat)
    {
        switch ($sFormat)
        {
            case 'xml':
                $this->oFile = new XMLWriter();
                $this->oFile->openMemory();
                $this->oFile->startDocument('1.0', 'UTF-8');
                $this->oFile->startElement('Items');
                break;
            
            case 'csv':
            case 'xls':
            case 'xlsx':
                $this->oFile = Excel::create($this->oFlow->name, function($oExcel)
                {
                    $oExcel->sheet('Table', function($oSheet) 
                    {
                        $oSheet->appendRow(array_keys($this->oFlow->columns));
                    });
                });
                break;
        }
    }
    
    private function setFlow($sToken)
    {
        $this->oFlow = DataflowRepository::findByField('token', $sToken)->first();
        
        $this->oRepository = new $this->oFlow->repository();
        $this->oRepository->setReturnCollection(false);
    }
    
    private function getIds()
    {
        if (!empty($this->aIds))
        {
            $oIds = collect($this->aIds);
        }
        else
        {
            $oIds = $this->oRepository
                ->findWhere($this->oFlow->where_clause, [$this->oRepository->getPrimaryKey()])
                ->pluck($this->oRepository->getPrimaryKey());
        }
        
        return $oIds->chunk(1000);
    }
    
    private function createFlow($sFormat)
    {
        $oIds = $this->getIds();
        $aLines = [];
        foreach ($oIds as $oId)
        {
            $oItems = $this->oRepository->findWhereIn(
                $this->oRepository->getPrimaryKey(), 
                $oId->toArray(),
                array_values($this->oFlow->columns)
            );
            
            switch ($sFormat)
            {
                case 'xml':
                    $aLines = [];
                    $this->buildFlow($aLines, $sFormat, $oItems);
                    file_put_contents(
                        $this->getPath().'/'.$this->oFlow->name.'-temp.'.$sFormat, 
                        $this->oFile->flush(true), 
                        FILE_APPEND
                    );
                    break;
                    
                case 'csv':
                case 'xls':
                case 'xlsx':
                    $this->oFile->getSheet()->rows($this->buildFlow($aLines, $sFormat, $oItems));
                    break;
                
                default:
                    $aLines = $this->buildFlow($aLines, $sFormat, $oItems);
                    break;
            }            
        }
        
        return $this->buildResponse($sFormat, $aLines);
    }
    
    private function buildFlow($aLines, $sFormat, $oItems)
    {
        foreach ($oItems as $oItem)
        {
            switch ($sFormat)
            {
                case 'xml':
                    $this->oFile->startElement('Item');
                    $this->buildLine($oItem, $sFormat);
                    $this->oFile->endElement();
                    break;

                default:
                    $aLines[] = $this->buildLine($oItem, $sFormat);
                    break;
            }
        }
        
        return $aLines;
    }
    
    private function buildResponse($sFormat, $aLines)
    {
        $sFile  = $this->getPath().'/'.$this->oFlow->name.'.'.$sFormat;
        switch ($sFormat)
        {
            case 'xml':
                $this->oFile->endElement();
                file_put_contents(
                    $this->getPath().'/'.$this->oFlow->name.'-temp.'.$sFormat, 
                    $this->oFile->flush(true), 
                    FILE_APPEND
                );
                $sXml = file_get_contents($this->getPath().'/'.$this->oFlow->name.'-temp.'.$sFormat);
                unlink($this->getPath().'/'.$this->oFlow->name.'-temp.'.$sFormat);
                file_put_contents($sFile, $sXml);
                
                return response($sXml, '200')->header('Content-Type', 'text/xml');
                break;

            case 'csv':
            case 'xls':
            case 'xlsx':
                $this->oFile->store($sFormat, $this->getPath())->export($sFormat);
                return '';
                break;
            
            case 'json':
                file_put_contents($sFile, json_encode($aLines));
                
                return response()->json($aLines);
                break;
        }
    }
    
    private function getPath()
    {
        return storage_path('dataflow/exports');
    }


    /**
     * Build a line of the CSV.
     *  
     * @param object $oArticle
     * @param object $oArticleVar
     * 
     * @return string $sLine
     */
    private function buildLine($oItem, $sFormat)
    {
        $aLine = [];
        
        foreach ($this->oFlow->columns as $sHead => $sAttribute)
        {
            switch ($sFormat)
            {
                case 'xml':
                    $this->oFile->writeElement($sHead, $this->getLastAttribute($oItem, $sAttribute));
                    break;
                
                default:
                    $aLine[$sHead] = $this->getLastAttribute($oItem, $sAttribute);
                    break;
            }
        }
        
        return $aLine;
    }
    
    protected function getLastAttribute($oItem, $sAttribute)
    {
        if (strpos($sAttribute, '.') !== false)
        {
            $aAttribute     = explode('.', $sAttribute);
            $sAttributeName = $aAttribute[0];
            $mAttribute     = $oItem->$sAttributeName;
            $this->takeFirstIfArray($mAttribute);

            $iTotalSubAttribute = count($aAttribute);

            for ($i = 1 ; $i < $iTotalSubAttribute ; $i++)
            {
                $sAttributeName = $aAttribute[$i];
                
                if (!is_object($mAttribute))
                {
                    continue;
                }
                
                $mAttribute     = $mAttribute->$sAttributeName;
                $this->takeFirstIfArray($mAttribute);
            }
            
            return $mAttribute;
        }
        else
        {
            return $oItem->$sAttribute;
        }
    }
    
    protected function takeFirstIfArray(&$mValue)
    {
        if (is_array($mValue) && !empty($mValue))
        {
            $mValue = array_values($mValue)[0];
        }
        
        if (is_array($mValue) && empty($mValue))
        {
            $mValue = '';
        }
    }
    
    private function getCache($sFormat)
    {
        $sFile = $this->getPath().'/'.$this->oFlow->name.'.'.$sFormat;
        if (file_exists($sFile))
        {
            switch ($sFormat)
            {
                case 'xml':
                    return response()->file($sFile);
                    break;

                case 'csv':
                case 'xls':
                case 'xlsx':
                    return response()->download($sFile);
                    break;

                case 'json':
                    $aJson = json_decode(file_get_contents($sFile), true);
                    return response()->json($aJson);
                    break;
            }
        }
        else
        {
            return $this->createFlow($sFormat);
        }
    }
    
    private function download($sFormat)
    {
        $sFile = $this->getPath().'/'.$this->oFlow->name.'.'.$sFormat;
        
        if (!file_exists($sFile))
        {
            $this->createFlow($sFormat);
        }
        
        return response()->download($sFile);
    }
}
