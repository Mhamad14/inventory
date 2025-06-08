 <div class="row">
     <div class="col-sm-12 col-md-6 col-lg-4">
         <div class="form-group">
             <label for="purchase_date">Purchase Date</label><span class="asterisk text-danger"> *</span>
             <input type="text" class="form-control" id="purchase_date" name="purchase_date" value="<?= $purchase['purchase_date'] ?>">
         </div>
     </div>
     <div class="col-md-3">
         <div class="form-group">
             <label for="batch_supplier">Supplier</label><span class="asterisk text-danger">*</span>
             <select class="batch_supplier form-control" id="batch_supplier" name="supplier_id">
             </select>
         </div>
     </div>

     <div class="col-md-1 supplier-add-btn">
         <span><button class="btn btn-icon btn-secondary edit_btn" data-url="admin/suppliers/create" id=""><i class="fas fa-plus"></i></button></span>
     </div>
     <div class="col-md-3">
         <div class="form-group">
             <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
             <select class="form-control" id="warehouse_id" name="warehouse_id">
                 <option value="" disabled <?= empty($purchase['warehouse_id']) ? 'selected' : '' ?>>Select warehouse</option>
                 <?php foreach ($warehouses as $warehouse) { ?>
                     <option value="<?= $warehouse['id'] ?>" <?= ($purchase['warehouse_id'] == $warehouse['id']) ? 'selected' : '' ?>>
                         <?= $warehouse['name'] ?>
                     </option>
                 <?php } ?>
             </select>
         </div>

     </div>
 </div>