{config_load file="$skin_config"}{ $config.Company.company_name }: {$lng.eml_order_cust_processed_subj|substitute:"orderid":$order.orderid}
