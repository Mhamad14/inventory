<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WarehouseBatchModel;

class WarehouseBatchController extends BaseController
{
    protected $batchModel;

    public function __construct()
    {
        $this->batchModel = new WarehouseBatchModel();
    }

    // 🧾 List all batches
    public function index()
    {
        $batches = $this->batchModel->findAll();
        return $this->response->setJSON($batches);
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

        $this->batchModel->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Batch created successfully'
        ]);
    }

    // 📝 Show specific batch
    public function show($id)
    {
        $batch = $this->batchModel->find($id);

        if (!$batch) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Batch not found']);
        }

        return $this->response->setJSON($batch);
    }

    // 🛠 Update batch
    public function update($id)
    {
        $data = $this->request->getJSON(true);

        $this->batchModel->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Batch updated successfully'
        ]);
    }

    // ❌ Delete batch
    public function delete($id)
    {
        $this->batchModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Batch deleted successfully'
        ]);
    }
}
