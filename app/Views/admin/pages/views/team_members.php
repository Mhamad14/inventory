<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('team members', 'Team members') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/team_members/create'); ?>" class="btn"  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('create_team_members', 'Create Team Members') ?>"  ><i class="fas fa-plus"></i> </a>
                </div>

            </div>
        </div>
        <?= session("message") ?>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table-striped" data-toggle="table" data-url="<?= site_url('admin/team_members/view_team_members') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                    data-search="true" data-show-columns="true" data-show-refresh="true"
                                    data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                    data-mobile-responsive="true" data-toolbar="" data-show-export="true"
                                    data-maintain-selected="true" data-export-types='["txt","excel"]' data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th data-field="team_member_id" data-sortable="true">Team Member Id</th>
                                            <th data-field="user_id" data-sortable="false">User Id</th>
                                            <th data-field="mobile" data-sortable="false">Mobile</th>
                                            <th data-field="first_name" data-sortable="false">First Name</th>
                                            <th data-field="last_name" data-sortable="false">Last Name</th>
                                            <th data-field="email" data-sortable="false">Email</th>
                                            <th data-field="operate" data-sortable="true">Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                                <script>
                                    function queryParams(params) {
                                        console.log(params); // Debug: Check parameters
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
    </section>
</div>