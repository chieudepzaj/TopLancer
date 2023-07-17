<?php
define("ROOTPATH", dirname(dirname(dirname(__DIR__))));
define("APPPATH", ROOTPATH."/php/");
require_once ROOTPATH . '/includes/autoload.php';
require_once ROOTPATH . '/includes/lang/lang_'.$config['lang'].'.php';
admin_session_start();
$pdo = ORM::get_db();

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
if($params['draw'] == 1)
    $params['order'][0]['dir'] = "desc";
//define index of column
$columns = array(
    0 =>'p.id',
    1 =>'p.product_name',
    2 =>'c.name',
    3 =>'p.created_at',
    4 =>'p.status'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ){
    $where .=" WHERE ";
    $where .=" ( p.product_name LIKE '".$params['search']['value']."%' ";
    $where .=" OR c.name LIKE '".$params['search']['value']."%' ";
    $where .=" OR cat.cat_name LIKE '".$params['search']['value']."%' )";
}




// getting total number records without any search
$sql = "SELECT p.*,
ct.name as cityname,
cat.cat_id as catid,
cat.cat_name as catname,
scat.sub_cat_id as subcatid,
scat.sub_cat_name as subcatname,
c.logo as company_image
FROM `".$config['db']['pre']."product_resubmit` as p
LEFT JOIN `".$config['db']['pre']."companies` as c ON c.id = p.company_id
LEFT JOIN `".$config['db']['pre']."cities` as ct ON ct.id = p.city
LEFT JOIN `".$config['db']['pre']."catagory_main` as cat ON cat.cat_id = p.category
LEFT JOIN `".$config['db']['pre']."catagory_sub` as scat ON scat.sub_cat_id = p.sub_category ";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if(isset($where) && $where != '') {
    $sqlTot .= $where;
    $sqlRec .= $where;
}

$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

$queryTot = $pdo->query($sqlTot);
$totalRecords = $queryTot->rowCount();
$queryRecords = $pdo->query($sqlRec);

//iterate on results row and create new index array of data
foreach ($queryRecords as $row) {
    $id = $row['id'];
    $product_id = $row['product_id'];
    $title = htmlspecialchars($row['product_name']);
    $ad_created_at  = timeAgo($row['created_at']);
    $ad_category = htmlspecialchars($row['catname']);
    $ad_status    = $row['status'];
    $featured = $row['featured'];
    $urgent = $row['urgent'];
    $highlight = $row['highlight'];
    $company_image = !empty($row['company_image'])?$row['company_image']:'default.png';
    $image = !empty($row['screen_shot'])?$row['screen_shot']:$company_image;

    $image_tag = '';
    if($config['job_image_field']){
        $image_tag = '<div class="pull-left m-r"><img class="img-avatar img-avatar-squre" src="'.$config['site_url'].'storage/products/'.$image.'"></div>';
    }


    $premium = '';
    if ($featured == "1"){
        $premium = $premium.'<span class="badge fs-12">Đáng quan tâm</span>';
    }

    if($urgent == "1") {
        $premium = $premium.'<span class="badge btn-danger fs-12">Khẩn cấp</span>';
    }

    if($highlight == "1") {
        $premium = $premium.'<span class="badge btn-primary fs-12">Hot trend</span>';
    }

    $status = '<span class="label label-info">Chờ phê duyệt</span>';

    $row0 = '<td>
                <label class="css-input css-checkbox css-checkbox-default">
                    <input type="checkbox" class="service-checker" value="'.$id.'" id="row_'.$id.'" name="row_'.$id.'"><span></span>
                </label>
            </td>';
    $row1 = '<td class="text-center">'.$image_tag.'
                <p class="font-500 m-b-0"><a href="post_detail.php?id='.$product_id.'&resubmit=1" target="_blank">'.$title.'</a></p>
                <p class="m-b-0">'.$premium.'</p>
                <p class="text-muted m-b-0">'.$ad_category.'</p>
            </td>';
    $row2 = '<td class="hidden-xs">'.$row['cityname'].'</td>';
    $row3 = '<td class="hidden-xs hidden-sm">'.$ad_created_at.'</td>';
    $row4 = '<td class="hidden-xs hidden-sm">'.$status.'</td>';
    $row5 = '<td class="text-center">
                <div class="btn-group">
                    <a href="post_detail.php?id='.$product_id.'&resubmit=1" class="btn btn-xs btn-default"><i class="ion-eye"></i></a>
                    <a href="javascript:void(0)" class="btn btn-xs btn-default item-approve" data-ajax-action="approveResubmitItem"><i class="ion-android-done"></i></a>
                    <a href="javascript:void(0)" class="btn btn-xs btn-default item-js-delete" data-ajax-action="deleteResubmitItem"><i class="ion-close"></i></a>
                </div>
            </td>';
    $value = array(
        "DT_RowId" => $product_id,
        0 => $row0,
        1 => $row1,
        2 => $row2,
        3 => $row3,
        4 => $row4,
        5 => $row5
    );
    $data[] = $value;
}

$json_data = array(
    "draw"            => intval( $params['draw'] ),
    "recordsTotal"    => intval( $totalRecords ),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
?>