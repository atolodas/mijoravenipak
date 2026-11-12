<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * PrestaShop 8/9 compatibility:
 * - removes deprecated alias hooks (header, updateCarrier)
 * - on PS 1.7.7+ moves order block from displayAdminOrder (page bottom) to displayAdminOrderMain
 * - removes legacy order list / PS 1.6 hooks which are not used on current PS version
 * - resizes Venipak carrier logos (were copied in original 834px size)
 *
 * @param MijoraVenipak $module
 *
 * @return bool
 */
function upgrade_module_1_3_0($module)
{
    // Carrier logos were copied in original size (834px) - PS 9 Hummingbird theme shows them in natural size
    $module->fixCarrierLogos();

    return $module->syncHooks();
}
