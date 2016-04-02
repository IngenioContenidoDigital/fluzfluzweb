<?php
include(realpath(dirname(__FILE__).'/SondaPayu.php'));

// instancia el objeto link en el contexto si no viene inicializado
if (empty(Context::getContext()->link))
	Context::getContext()->link = new Link();

$sonda = new SondaPayu();
$sonda->updatePendyngOrdesConfirmation();
$sonda->updatePendyngOrdes();


