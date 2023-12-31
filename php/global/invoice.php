<?php

$id = $_GET['id'];

$rows = ORM::for_table($config['db']['pre'] . 'transaction')->find_one($id);

if (isset($rows['id'])) {
    if (isset($_SESSION['user']['id']) || isset($_SESSION['admin']['id'])) {

        if(isset($_SESSION['user']['id']) && ($rows['user_id'] != $_SESSION['user']['id']) ){
            /* redirect to 404 */
            error(__("Page Not Found"), __LINE__, __FILE__, 1);
        }

        $billing = json_decode((string)$rows['billing'], true);
        $billing_country = get_countryName_by_code(
            isset($billing['country'])
                ? $billing['country']
                : get_user_option($_SESSION['user']['id'], 'billing_country')
        );

        $invoice_date = date('d/m/Y', $rows['transaction_time']);
        $item_price = !empty($rows['base_amount'])?$rows['base_amount']:$rows['amount'];
        $currency_code = ($rows['currency_code'] != null)? $rows['currency_code']: $config['currency_code'];

        /* Get payment gateway */
        $payment_gateway = ORM::for_table($config['db']['pre'] . 'payments')
            ->where('payment_folder', $rows['transaction_gatway'])
            ->find_one();
        if(!$payment_gateway){
            $payment_title = $rows['transaction_gatway'];
        }else{
            $payment_title = $payment_gateway['payment_title'];
        }
        $inclusive_tax = $exclusive_tax = 0;
        $price_without_inclusive = $item_price - $inclusive_tax;

        $premium = '';
        if($rows['transaction_method'] == 'membership'){
            $premium = __("Membership");
        }else if($rows['transaction_method'] == 'project_fee'){
            $premium = __("Project Fee");
        }
        else if($rows['transaction_method'] == 'deposit'){
            $premium = __("Deposit amount to wallet");
        }else if($rows['transaction_method'] == 'milestone_created' || $rows['transaction_method'] == 'milestone_released'){
            $premium = __("Milestone Payment");
        }
        else{
            $featured = $rows['featured'];
            $urgent = $rows['urgent'];
            $highlight = $rows['highlight'];

            if ($featured == "1")
                $premium = $premium .' '. __("Featured");

            if ($urgent == "1")
                $premium = $premium .' '. __("Urgent");

            if ($highlight == "1")
                $premium = $premium .' '. __("Highlight");

        }

        //Print Template
        HtmlTemplate::display('global/invoice', array(
            'invoice_date' => $invoice_date,
            'invoice_id' => $rows['id'],
            'item_name' => $rows['product_name'].'<br>'.$premium,
            'paid_via' => $payment_title,
            'item_amount' => price_format($price_without_inclusive),
            'total_amount' => price_format($rows['amount']),
            'billing_details_type' => isset($billing['type']) ? $billing['type'] : get_user_option($_SESSION['user']['id'], 'billing_details_type'),
            'billing_tax_id' => isset($billing['tax_id']) ? $billing['tax_id'] : get_user_option($_SESSION['user']['id'], 'billing_tax_id'),
            'billing_name' => isset($billing['name']) ? $billing['name'] : get_user_option($_SESSION['user']['id'], 'billing_name'),
            'billing_address' => isset($billing['address']) ? $billing['address'] : get_user_option($_SESSION['user']['id'], 'billing_address'),
            'billing_city' => isset($billing['city']) ? $billing['city'] : get_user_option($_SESSION['user']['id'], 'billing_city'),
            'billing_state' => isset($billing['state']) ? $billing['state'] : get_user_option($_SESSION['user']['id'], 'billing_state'),
            'billing_zipcode' => isset($billing['zipcode']) ? $billing['zipcode'] : get_user_option($_SESSION['user']['id'], 'billing_zipcode'),
            'BILLING_COUNTRY' => $billing_country
        ));
        exit;
    }
}

/* redirect to 404 */
error(__("Page Not Found"), __LINE__, __FILE__, 1);