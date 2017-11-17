<?php

/**
 * @file
 *          This file is part of the CountWord library.
 *
 * @author  Andhika Kurnia <kurniaandhika18@gmail.com>
 * @date    2017-11-17
 * @license LGPLv3
 * @url     <https://github.com/andhika18/CountWord>
 *
 */


class CountWord
{
	function __construct()
	{

	}

	public function count($filename){
		$fileext = $filename->getClientOriginalExtension();
		if($fileext=="docx"){
            return str_word_count($this->read_docx($filename));
        }else if($fileext=="doc"){
            return str_word_count($this->read_doc($filename)); 
        }else if($fileext=="pdf"){
            return str_word_count($this->read_pdf($filename)); 
        }else if($fileext=="rtf"){
            return str_word_count($this->read_rtf($filename)); 
        }else if($fileext=="txt"){
            return str_word_count($this->read_txt($filename)); 
        }else if($fileext=="odt"){
            return str_word_count($this->read_odt($filename)); 
        }
	}
	public function text($filename){
		$fileext = $filename->getClientOriginalExtension();
		if($fileext=="docx"){
        	return print_r($this->read_docx($filename)); 
        }else if($fileext=="doc"){
        	return print_r($this->read_doc($filename)); 
        }else if($fileext=="pdf"){
            return print_r($this->read_pdf($filename)); 
        }else if($fileext=="rtf"){
            return print_r($this->read_rtf($filename)); 
        }else if($fileext=="txt"){
            return print_r($this->read_txt($filename)); 
        }else if($fileext=="odt"){
            return str_word_count($this->read_odt($filename)); 
        }
		
	}
	private function read_docx($filename){
        $striped_content = '';
        $content = '';

        $zip = zip_open($filename);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

    private function read_doc($filename) {
        $fileHandle = fopen($filename, "r");
        $line = @fread($fileHandle, filesize($filename));   
        $lines = explode(chr(0x0D),$line);
        $outtext = "";
        foreach($lines as $thisline)
          {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE)||(strlen($thisline)==0))
              {
              } else {
                $outtext .= $thisline." ";
              }
          }
        $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
        return $outtext;
    }

    private function read_pdf($filename){
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($filename);
        $bullet = '•';

        $text = $pdf->getText();
        $text = str_replace($bullet, " ", $text);
        return $text;
    }
    private function read_txt($filename) {
        $line = file_get_contents($filename);   
        
        return $line;
    }
    private function read_odt($filename){
        $striped_content = '';
        $content = '';

        $zip = zip_open($filename);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "content.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }
}
