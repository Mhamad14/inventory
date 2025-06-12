<div class="main-content">
    <div class="section">
        <div class="section-header">
            <h1><?= labels('employees', 'Employees') ?></h1>
            <div class="section-header-breadcrumb d-flex align-items-center gap-2">
                <a class="btn btn-primary text-white me-2" href="<?= base_url('admin/employees/new'); ?>" title="<?= labels('add_employee', 'Add employee') ?>">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>
        <?= session("message") ?>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover table-borderd"
                                data-auto-refresh="true"
                                data-show-columns="true"
                                data-show-toggle="true"
                                data-show-refresh="true"
                                data-toggle="table"
                                data-search-highlight="true"
                                data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                data-url="<?= base_url('admin/employees/employees_table'); ?>"
                                data-side-pagination="server"
                                data-pagination="true"
                                data-search="true"
                                data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true" data-visible="false"><?= labels('id', 'ID') ?></th>
                                        <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                        <th data-field="position" data-sortable="true"><?= labels('position', 'Position') ?></th>
                                        <th data-field="position_id" data-sortable="true" data-visible="false"><?= labels('position_id', 'Position_id') ?></th>
                                        <th data-field="address" data-sortable="true"><?= labels('address', 'Address') ?></th>
                                        <th data-field="mobile" data-sortable="true"><?= labels('mobile', 'Contact Number') ?></th>
                                        <th data-field="salary" data-sortable="true"><?= labels('salary', 'Salary') ?></th>
                                        <th data-field="action"><?= labels('action', 'Action') ?></th>
                                    </tr>
                                </thead>
                            </table>
                            <script>
                                function queryParams(params) {
                                    return {
                                        limit: params.limit,
                                        offset: params.offset,
                                        sort: params.sort,
                                        search: params.search
                                    };
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
