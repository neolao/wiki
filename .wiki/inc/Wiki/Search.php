<?php
/**
 * The search
 */
class Wiki_Search
{
    /**
     * Data of the search engine
     *
     * @var Zend_Search_Lucene_Interface
     */
    protected $_data;



    /**
     * Constructor
     */
    public function __construct()
    {
        $dataPath = DATA_PATH.'/search';

        try {
            $this->_data = Zend_Search_Lucene::open($dataPath);
        } catch (Exception $error) {
            $this->_data = Zend_Search_Lucene::create($dataPath);
        }
    }

    /**
     * Index a file
     *
     * @param   string  $filePath   The file path
     */
    public function index($filePath)
    {
        $content = file_get_contents($filePath);
        $modificationTime = filemtime($filePath);
        $checksum = md5($content);

        // Get the document
        $hits = $this->_data->find('path:'.$filePath);
        if (count($hits) > 0) {
            $hit = $hits[0];
            $document = $hit->getDocument();

            // If the checksums are the same, no need to update
            if ($checksum === $document->checksum) {
                return;
            }

            // Delete the document
            $this->_data->delete($hit);
        }

        // Create a new document
        Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());
        $document = new Zend_Search_Lucene_Document();
        $document->addField(Zend_Search_Lucene_Field::keyword('path', $filePath));
        $document->addField(Zend_Search_Lucene_Field::keyword('modificationTime', $modificationTime));
        $document->addField(Zend_Search_Lucene_Field::keyword('checksum', $checksum));
        $document->addField(Zend_Search_Lucene_Field::unStored('content', $content, 'utf-8'));
        $this->_data->addDocument($document);

        // Commit the changes
        $this->_data->commit();
        $this->_data->optimize();
    }

    /**
     * Search into files
     *
     * @param   string      $query      The query
     * @return  array                   The file list
     */
    public function find($query)
    {
        $result = array();
        $hits = $this->_data->find($query);
        foreach ($hits as $hit) {
            $document = $hit->getDocument();
            $result[] = $document->path;
        }

        return $result;
    }

    /**
     * Get the document count
     *
     * @return  int                     Document count
     */
    public function getDocumentCount()
    {
        return $this->_data->numDocs();
    }
}