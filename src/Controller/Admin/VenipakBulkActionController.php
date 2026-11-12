<?php

namespace MijoraVenipak\Controller\Admin;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class VenipakBulkActionController extends FrameworkBundleAdminController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function bulkGenerateLabels(Request $request)
    {
        $orders = $this->getBulkOrderIds($request);
        // Container service access ($this->get()) is deprecated on PS 9
        $module_legacy = \Module::getInstanceByName('mijoravenipak');

        $warehouse_groups = $module_legacy->formatWarehousesOrderGroups($orders);
        if(!empty($warehouse_groups))
        {
            foreach ($warehouse_groups as $group)
            {
                $response = $module_legacy->bulkActionSendLabels($group);
                if(isset($response['errors']))
                {
                    $this->flashErrors($response['errors']);
                }
                if(isset($response['success']))
                {
                    $this->addFlash('success', $response['success']);
                }
            }
        }
        else
        {
            $this->flashErrors(["Could not group orders by warehouses."]);
        }
        return $this->redirectToRoute('admin_orders_index');
    }


    /**
     * @param Request $request
     *
     * @return Response
     */
    public function bulkPrintLabels(Request $request)
    {
        $orders = $this->getBulkOrderIds($request);
        // Container service access ($this->get()) is deprecated on PS 9
        $module_legacy = \Module::getInstanceByName('mijoravenipak');
        $module_legacy->bulkActionPrintLabels($orders);
        return $this->redirectToRoute('admin_orders_index');
    }

    /**
     * Symfony 6 (PS 9) InputBag::get() throws exception on array values, so read all params
     * (works on PS 1.7.7 / 8 / 9)
     *
     * @param Request $request
     *
     * @return int[]
     */
    private function getBulkOrderIds(Request $request)
    {
        $params = $request->request->all();
        $orders = isset($params['order_orders_bulk']) ? $params['order_orders_bulk'] : [];

        return array_map('intval', is_array($orders) ? $orders : [$orders]);
    }
}
