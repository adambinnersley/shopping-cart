<?php
namespace ShoppingCart\FPDF;

class PDF_JavaScript extends \FPDF {

    var $javascript;
    var $n_js;

    /**
     * 
     * @param string $script
     */
    protected function includeJS($script) {
        $this->javascript = $script;
    }

    protected function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }

    protected function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    protected function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
}

class FPDF extends PDF_JavaScript {
    
    /**
     * 
     * @param string|boolean $printer
     */
    public function AutoPrint($printer='')
    {
        $script = '';
        if(is_string($printer)) {
            $printer = str_replace('\\', '\\\\', $printer);
            $script .= "var pp = getPrintParams();";
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
            $script .= "pp.printerName = '$printer'";
            $script .= "print(pp);";
        }
        elseif($printer === true) {
            $script .= 'print(true);';
        }
        $this->includeJS($script);
    }
}