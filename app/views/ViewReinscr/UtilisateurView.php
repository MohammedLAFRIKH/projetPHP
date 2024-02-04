<?php

namespace App\Views\ViewReinscr;

class UtilisateurView {
    private $headerView;
    private $footerView;

    public function __construct(HeaderView $headerView, FooterView $footerView) {
        $this->headerView = $headerView;
        $this->footerView = $footerView;
    }

    
}
?>
