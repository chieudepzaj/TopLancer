<?php
define("ROOTPATH", dirname(dirname(dirname(__DIR__))));
define("APPPATH", ROOTPATH."/php/");
require_once ROOTPATH . '/includes/autoload.php';
require_once ROOTPATH . '/includes/lang/lang_'.$config['lang'].'.php';
admin_session_start();
$pdo = ORM::get_db();

$info = ORM::for_table($config['db']['pre'].'qbm_banners')->find_one($_GET['id']);
$id = $info['id'];
$status = $info['status'];
$user = ORM::for_table($config['db']['pre'].'user')->find_one($info['user_id']);

?>

<header class="slidePanel-header overlay">
    <div class="overlay-panel overlay-background vertical-align">
        <div class="service-heading">
            <h2>Trạng Thái Banner Quảng Cáo</h2>
        </div>
        <div class="slidePanel-actions">
            <div class="btn-group-flat">
                <button type="button"
                    class="btn btn-floating btn-warning btn-sm waves-effect waves-float waves-light margin-right-10"
                    id="post_sidePanel_data"><i class="icon ion-android-done" aria-hidden="true"></i></button>
                <button type="button"
                    class="btn btn-pure btn-inverse slidePanel-close icon ion-android-close font-size-20"
                    aria-hidden="true"></button>
            </div>
        </div>
    </div>
</header>
<div class="slidePanel-inner">
    <div class="panel-body">
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">

                <div class="white-box">
                    <div id="post_error"></div>
                    <form name="form2" class="form form-horizontal" method="post" data-ajax-action="advertiseEdit"
                        id="sidePanel_form">
                        <div class="form-body">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>ID:</label>
                                    <input type="text" disabled class="form-control" value="<?php echo $id ?>">
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Trạng Thái</label>
                                    <select name="status" class="form-control">
                                        <option value="success" <?php if($status == 'success') echo "selected"; ?>>
                                            Thành Công</option>
                                        <option value="pending" <?php if($status == 'pending') echo "selected"; ?>>
                                            Chờ Phê Duyệt</option>
                                        <option value="reject" <?php if($status == 'reject') echo "selected"; ?>>Từ Chối
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Tên Người Dùng:</label>
                                    <input type="text" disabled class="form-control"
                                        value="<?php echo $user['username']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Số Tiền:</label>
                                    <input type="text" disabled class="form-control"
                                        value="<?php echo $info['amount']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Tiêu Đề:</label>
                                    <input type="text" disabled class="form-control"
                                        value="<?php echo $info['title']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Url:</label>
                                    <input type="text" disabled class="form-control"
                                        value="<?php echo $info['url']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Ảnh:</label>
                                    <img style="width: inherit;"
                                        src="<?php echo $config['site_url']; ?>/storage/banner_advertise/<?php echo $info['file']; ?>"
                                        alt="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Số Ngày:</label>
                                    <input type="text" disabled class="form-control"
                                        value="<?php echo $info['days_purchased']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label>Ngày tạo:</label>
                                    <input type="text" disabled class="form-control"
                                        value="<?php echo date('h:i:s d/m/Y', $info['registered']); ?>">
                                </div>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>