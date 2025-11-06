<?php
function seo_title(?string $title=null){ return service('seosearch')->title($title); }
function seo_desc(?string $desc=null){ return service('seosearch')->description($desc); }
function seo_head(){ return service('seosearch')->renderHead(); }
