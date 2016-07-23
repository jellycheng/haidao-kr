<?php
addhook('create_order','module/order');
addhook('pay_success','module/order');
addhook('confirm_order','module/order');
addhook('skus_delivery','module/order');
addhook('delivery_finish','module/order');
addhook('order_finish','module/order');