<?php

namespace SimpleExcel\Writer;

/**
 * SimpleExcel class for writing CSV Spreadsheet
 *  
 * @author  Faisalman
 * @package SimpleExcel
 */
class CSVWriter extends BaseWriter implements IWriter
{
    /**
     * Defines content-type for HTTP header
     * 
     * @access  protected
     * @var     string
     */
    protected $content_type = 'text/csv';
    /**
     * Defines enclosure char
     *
     * @var string
     * @var     string
     */
    protected $enclosure = '"';
    /**
     * Defines delimiter char
     * 
     * @access  protected
     * @var     string
     */
    protected $delimiter = ',';

    /**
     * Defines file extension to be used when saving file
     * 
     * @access  protected
     * @var     string
     */
    protected $file_extension = 'csv';
    
    /**
     * Get document content as string
     * 
     * @return  string  Content of document
     */
    public function saveString(){
        $fp = fopen('php://temp', 'r+');
        foreach ($this->tabl_data as $row) {
            $this->fputcsv($fp, $row, $this->delimiter,$this->enclosure);
        }
        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);
        return $content;
    }

    /**
     * Set character for delimiter
     * 
     * @param   string  $delimiter  Commonly used character can be a comma, semicolon, tab, or space
     * @return  void
     */
    public function setDelimiter($delimiter){
        $this->delimiter = $delimiter;
    }

    /**
     * Custom fputcsv
     * @param $handle
     * @param array $fields
     * @param string $delimiter
     * @param string $enclosure
     * @return int
     */
    public function fputcsv($handle, $fields = array(), $delimiter = ',', $enclosure = '"') {
        $str = '';
        $escape_char = '\\';
        foreach ($fields as $value) {
            $str .= $enclosure.$value.$enclosure.$delimiter;
        }
        $str = substr($str,0,-1);
        $str .= "\n";
        return fwrite($handle, $str);
    }
}
?>
