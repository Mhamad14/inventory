<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Purchases_items_model;
use App\Models\Purchases_model;
use App\Models\Status_model;
use App\Models\Suppliers_model;
use App\Models\warehouse_batches_model;
use App\Models\WarehouseModel;

class WarehouseBatchController extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;

    protected $batch_model;
    protected $purchase_model;
    protected $purchases_items_model;
    protected $status_model;
    protected $warehouses_model;

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'common']);
        $this->configIonAuth = config('IonAuth');

        $this->batch_model = new warehouse_batches_model();
        $this->purchase_model = new Purchases_model();
        $this->status_model = new Status_model();
        $this->warehouses_model = new WarehouseModel();
        $this->purchases_items_model = new Purchases_items_model();
    }

    // 🧾 List all batches
    public function index($purchase_id = '')
    {
        $data = getdata(
            'purchase',
            $this->purchase_model->getPurchase($purchase_id),
            FORMS . 'Batches/show',
            'status_list',
            $this->status_model->get_status(session('business_id')),
            'warehouses',
            $this->warehouses_model->where('business_id', session('business_id'))->findAll()
        );

        $data['order_type'] = 'return';
        $data['purchase_id'] = $purchase_id;
        session()->set('purchase_id', $purchase_id);

        return view("admin/template", $data);
    }

    public function batches_table()
    {
        $batches = $this->batch_model->getBatches(session('purchase_id'));
        $total =
            $rows = [];
        foreach ($batches as $batch) {
            $rows[] = $this->prepareBatchesRow($batch);
        }

        return $this->response->setJSON([
            'total' => $total[0]['total'] ?? 0,
            'rows' => $rows
        ]);
    }

    function prepareBatchesRow(array $item): array
    {
        $item['image_url'] = base_url($item['image']);
        $rowData = htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); // Convert the entire row to JSON and escape it for HTML
        $actions = "<a href='javascript:void(0)' data-row='{$rowData}'  class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Batch Update' data-bs-toggle='modal' data-bs-target='#batch_edit_modal'><i class='bi bi-pen'></i></a>";
        $img = '<div class="image-box-100 "><a href="' . base_url($item['image'])  . '" data-lightbox="image-1">
             <img src="' . base_url($item['image']) . '" class="image-100 image-box-100 img-fluid" />
            </a></div>';

        $result = [
            'id' => $item['id'],
            'image' => $img,
            'name' => $item['product_name'] . " - " . $item['variant_name'],
            'quantity' => $item['quantity'],
            'cost_price' => currency_location(decimal_points($item['cost_price'])),
            'sell_price' => currency_location(decimal_points($item['sell_price'])),
            'expire' => $item['expiration_date'],
            'discount' => $item['discount'],
            'total' => currency_location(decimal_points(($item['quantity'] * $item['cost_price']) - $item['discount'])),
            'status' => $item['status'],
            'actions' => $actions
        ];

        return $result;
    }
    public function get_suppliers()
    {
        $search = $this->request->getGet('search');
        $results = [];
        log_message('debug', 'welcome');
        if ($search && strlen($search) >= 1) {
            try {
                $suppliers_model = new Suppliers_model();
                $response = $suppliers_model->search_suppliers($search);
                $suppliers = json_decode($response)->data ?? [];

                foreach ($suppliers as $supplier) {
                    if ($supplier->status) {
                        $results[] = $supplier;
                    }
                }
                log_message('debug', print_r($results, true));
            } catch (\Exception $e) {
                log_message('error', 'Supplier search failed: ' . $e->getMessage());
            }
        }

        return view('admin/pages/forms/Batches/partials/update_purchase_order/suplier_results', ['suppliers' => $results]);
    }

    public function Returned_batches_table()
    {
        $returned_batches = $this->batch_model->getReturnedBatches(session('purchase_id'));
        $total =
            $rows = [];
        foreach ($returned_batches as $batch) {
            $rows[] = $this->prepareReturnedBatchRow($batch);
        }

        return $this->response->setJSON([
            'total' => $total[0]['total'] ?? 0,
            'rows' => $rows
        ]);
    }

    public function prepareReturnedBatchRow(array $item): array
    {
        $item['image_url'] = base_url($item['image']);

        $actions = "<a href='javascript:void(0)' data-returned_batch_id='{$item['id']}' id = 'btn_returned_delete' class='btn btn-danger btn-sm btn-delete' data-toggle='tooltip' title='Delete Batch'>
                        <i class='bi bi-trash'></i>
                    </a>";
        $img = '<div class="image-box-100 "><a href="' . base_url($item['image'])  . '" data-lightbox="image-1">
             <img src="' . base_url($item['image']) . '" class="image-100 image-box-100 img-fluid" />
            </a></div>';

        $result = [
            'id' => $item['id'],
            'image' => $img,
            'name' => $item['product_name'] . " - " . $item['variant_name'],
            'quantity' => $item['quantity'],
            'cost_price' => currency_location(decimal_points($item['cost_price'])),
            'return_price' => currency_location(decimal_points($item['return_price'])),
            'return_date' => $item['return_date'],
            'return_reason' => $item['return_reason'],
            'return_total' => currency_location(decimal_points(($item['quantity'] * $item['return_price']))),
            'actions' => $actions
        ];

        return $result;
    }

    public function update_purchase_item_status()
    {
        $status = $this->request->getPost('status');
        $order_id = $this->request->getPost('order_id');

        $rules = [];
        if ($this->request->getPost('status')) {
            $rules['status'] = 'required';
        }
        if ($this->request->getPost('order_id')) {
            $rules['order_id'] = 'numeric';
        }


        $this->validation->setRules($rules);
        if (!$this->validation->run($_POST)) {
            $errors = $this->validation->getErrors();
            $response = csrfResponseData([
                'success' => false,
                'message' => $errors,
            ]);
            return $this->response->setJSON($response);
        }

        update_details(['status' => $status], ['id' => $order_id], 'purchases_items');
        $response = csrfResponseData([
            'success' => true,
            'message' => "Order status updated successfully!",
        ]);
        return $this->response->setJSON($response);
    }

    // ➕ Create new batch
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$this->validate([
            'purchase_item_id'     => 'required|is_natural_no_zero',
            'business_id'          => 'required|is_natural_no_zero',
            'product_variant_id'   => 'required|is_natural_no_zero',
            'warehouse_id'         => 'required|is_natural_no_zero',
            'batch_number'         => 'required|string|max_length[50]',
            'quantity'             => 'required|numeric',
            'cost_price'           => 'required|numeric',
        ])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $this->batch_model->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Batch created successfully'
        ]);
    }

    public function add_to_existing_purchase()
    {
        $data = $this->request->getJson(true);
        log_message('debug', 'Add to existing purchase data: ' . print_r($data, true));

        $db = \Config\Database::connect();
        $db->transStart();

        try {

            // #1 insert into purchases_items
            $isItemInserted = $this->purchases_items_model->insert([
                'purchase_id' => $data['purchase_id'],
                'product_variant_id' => $data['variant_id'],
                'quantity' => $data['quantity'],
                'price' => $data['cost_price'],
                'discount' => $data['discount'] ?? 0,
                'status' => $data['status'],
            ]);
            if (!$isItemInserted) {
                throw new \Exception('Failed to insert purchase item');
            }

            // #2 update purchases
            $amountToAdd = $data['quantity'] * $data['cost_price'];
            $isPurchaseUpdated = $this->purchase_model
                ->set('total', 'total + ' . $amountToAdd, false)
                ->where('id', $data['purchase_id'])
                ->update();
            if (!$isPurchaseUpdated) {
                throw new \Exception('Failed to update purchase total');
            }

            // #3 insert into batches
            // #3.1 prepare batch data
            $batch_data = (object)[
                'id' => $data['variant_id'],
                'qty' => $data['quantity'],
                'price' => $data['cost_price'],
                'sell_price' => $data['sell_price'] ?? 0,
                'expire' => $data['expire_date'] ?? null,
            ];
            // #3.2 get product variant details
            $purchase_item_id = $this->purchases_items_model->getInsertID();
            $isBatchInserted = $this->batch_model->saveBatch(
                $purchase_item_id,
                $data['warehouse_id'],
                $batch_data,
            );
            // #3.3 update warehouse stock
            if (!$isBatchInserted) {
                throw new \Exception('Failed to insert batch');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $this->response->setJSON(csrfResponseData([
                'success' => true,
                'message' => 'Item added successfully',
            ]));
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Transaction error: ' . $e->getMessage());
            return $this->response->setJSON(csrfResponseData([
                'success' => false,
                'message' => 'An error occurred while adding to existing purchase: ' . $e->getMessage()
            ]));
        }





        // if (!$this->validate([
        //     'variant_id'   => 'required|is_natural_no_zero',
        //     'quantity'     => 'required|numeric|greater_than[0]',
        //     'cost_price'   => 'required|numeric|greater_than_equal_to[0]',
        //     'sell_price'   => 'required|numeric|greater_than_equal_to[0]',
        //     'expire_date'  => 'permit_empty|valid_date[Y-m-d]',
        //     'discount'     => 'permit_empty|numeric|greater_than_equal_to[0]',
        //     'status'       => 'permit_empty|string|max_length[20]'
        // ])) {
        //     return $this->response->setStatusCode(400)->setJSON([
        //         'status' => 'error',
        //         'errors' => $this->validator->getErrors()
        //     ]);
        // }

        // $this->batch_model->addToExistingPurchase($data);

        // return $this->response->setJSON([
        //     'status' => 'success',
        //     'message' => 'Batch added successfully'
        // ]);

    }
    // to update batch
    public function save_batch()
    {
        $data = $this->request->getPost();
        $rules = [
            'quantity'    => 'required|numeric|greater_than[0]',
            'cost_price'  => 'required|numeric|greater_than_equal_to[0]',
            'sell_price'  => 'required|numeric|greater_than_equal_to[0]',
            'discount'    => 'required|numeric|greater_than_equal_to[0]',
            'expire'      => 'required|valid_date[Y-m-d]'
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validation->getErrors()
            ]);
        }

        $batch_query = $this->batch_model->update_batch($data['save_batch_id'], $data['item_id'], $data['quantity'], $data['cost_price'], $data['sell_price'], $data['discount'], $data['expire'], $data['save_product_variant_id']);

        if ($batch_query) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Batch saved successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occured during the update!'
            ]);
        }
    }

    public function update_purchase()
    {
        $request = $this->request;

        $rules = [
            'purchase_id'     => 'required',
            'supplier_id'     => 'required|is_natural_no_zero',
            'warehouse_id'    => 'required|is_natural_no_zero',
            'purchase_date'   => 'required|valid_date[Y-m-d]',
            'order_discount'  => 'permit_empty|numeric',
            'shipping'        => 'permit_empty|numeric',
            'status'          => 'required|is_natural_no_zero',
            'payment_status'  => 'required|in_list[fully_paid,partially_paid,unpaid,cancelled]',
            'final_total'     => 'required|numeric|greater_than_equal_to[0]',
        ];

        $data = $request->getPost();

        // Manually validate amount_paid if partially_paid
        if ($data['payment_status'] === 'partially_paid') {
            if (!isset($data['amount_paid']) || floatval($data['amount_paid']) <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Amount Paid must be greater than 0."
                ]);
            }
            if (floatval($data['amount_paid']) > floatval($data['final_total'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Amount Paid cannot exceed the final total."
                ]);
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // If everything is valid, continue to update

        $updateData = [
            'supplier_id'      => $data['supplier_id'],
            'warehouse_id'     => $data['warehouse_id'],
            'purchase_date'    => $data['purchase_date'],
            'discount'         => $data['order_discount'],
            'delivery_charges' => $data['shipping'],
            'status'           => $data['status'],
            'payment_status'   => $data['payment_status'],
            'amount_paid'      => $data['amount_paid'] ?? 0,
            'total'            => $data['final_total'],
            'message'          => $data['message'] ?? '',
            'vendor_id'        => session('user_id')
        ];

        $isPurchaseUpdated = $this->purchase_model->update($data['purchase_id'], $updateData);
        if ($isPurchaseUpdated) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Purchase order saved successfully."
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => "An error occured during purchase order update."
            ]);
        }
    }

    public function delete_batch()
    {
        $data = $this->request->getPost();

        $rules = [
            'item_id'    => 'required|numeric|greater_than[0]',
            'save_batch_id'  => 'required|numeric|greater_than_equal_to[0]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validation->getErrors()
            ]);
        }

        $batch_query = $this->batch_model->delete_batch($data['save_batch_id'], $data['item_id']);

        if ($batch_query) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Batch saved successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occured during the update!'
            ]);
        }
    }

    public function delete_returned_batch($id = null)
    {
        $isBatchDeleted = $this->batch_model->delete_returned_batch($id);
        if ($isBatchDeleted) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Returned Item Deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occured during the delete!'
            ]);
        }
    }

    public function return_batch()
    {

        $data = $this->request->getPost();

        $rules = [
            'return_quantity'    => 'required|numeric|greater_than[0]',
            'return_price'  => 'required|numeric|greater_than_equal_to[0]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validation->getErrors()
            ]);
        }

        $batch_query = $this->batch_model->return_batch($data['return_data'], $data['return_price'], $data['return_reason'], $data['return_quantity'], $data['return_date']);

        if ($batch_query) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Batch Returned successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occured during the update!'
            ]);
        }
    }
}
