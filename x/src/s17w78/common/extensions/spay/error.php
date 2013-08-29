<?php
/**
 * Order
 */
/* order amount must be number */
define('ORDER_AMOUNT_NOT_NUMERIC', 6001);

/* order status error */
define('ORDER_STATUS', 6002);

/* order type error */
define('ORDER_TYPE', 6003);

/* order pay param not found */
define('ORDER_PAY_PARAM_NOT_FOUND', 6004);

/* order charge param not found */
define('ORDER_CHARGE_PARAM_NOT_FOUND', 6005);

/*
 * payment
 */
/* communicate type unknown */
define("PAYMENT_UNKNOW_COMMUNICATE_TYPE", 8001);

/* pay method not find*/
define("PAYMENT_UNKNOW_PAY_METHOD", 8002);

/* channel id unkown */
define("PAYMENT_UNKNOW_CHANNEL", 8003);

/* need order not found */
define("PAYMENT_NEED_ORDER", 8004);

/* need pay param */
define("PAYMENT_NEED_PAY_PARAM", 8005);

/* pay create fail */
define("PAYMENT_PAY_CREATE_FAIL", 8006);

/* error on request to pay channel */
define("PAYMENT_ERROR_ON_REQUEST", 8007);

/* get pay return body failed */
define("PAYMENT_RETURN_BODY_NOT_VALID", 8008);

/* unkonw charge channel */
define("PAYMENT_UNKNOW_CHARGE_CHANNEL", 8009);

/* unknow charge url */
define("PAYMENT_UNKNOW_CHARGE_URL", 8010);

/* save order unkown error */
define("PAYMENT_SAVE_ORDER_ERROR", 8011);

/* payment error on process outer data */
define("PAYMENT_ERROR_ON_GET_OUT_DATA", 8012);

/* payment error channel response error */
define("PAYMENT_CHANNEL_RESPONSE_ERROR", 8013);

/* payment error unknow app */
define("PAYMENT_UNKONW_APP", 8014);

/* no user to charge */
define("CHARGE_USER_NOT_FOUND", 8015);

/* save user gold error */
define("PAYMENT_SAVE_USERGOLD_ERROR", 8016);