<?php return array (
  'App\\Providers\\EventServiceProvider' => 
  array (
    'App\\Events\\OrderPaid' => 
    array (
      0 => 'App\\Listeners\\StockAdjustmentListener@handleOrderPaid',
      1 => 'App\\Listeners\\DistributeOrderProceedsListener@handle',
    ),
    'App\\Events\\OrderCancelled' => 
    array (
      0 => 'App\\Listeners\\StockAdjustmentListener@handleOrderCancelled',
    ),
    'App\\Events\\OrderRefunded' => 
    array (
      0 => 'App\\Listeners\\StockAdjustmentListener@handleOrderRefunded',
    ),
    'App\\Events\\PaymentWebhookReceived' => 
    array (
      0 => 'App\\Listeners\\HandlePaymentWebhook',
    ),
  ),
  'Illuminate\\Foundation\\Support\\Providers\\EventServiceProvider' => 
  array (
    'Illuminate\\Auth\\Events\\Login' => 
    array (
      0 => 'App\\Listeners\\ActivityLoggerSubscriber@handleUserLogin',
    ),
    'Illuminate\\Auth\\Events\\Logout' => 
    array (
      0 => 'App\\Listeners\\ActivityLoggerSubscriber@handleUserLogout',
    ),
    'Illuminate\\Auth\\Events\\Registered' => 
    array (
      0 => 'App\\Listeners\\ActivityLoggerSubscriber@handleUserRegistered',
    ),
    'App\\Events\\OrderPaid' => 
    array (
      0 => 'App\\Listeners\\ActivityLoggerSubscriber@handleOrderPaid',
      1 => 'App\\Listeners\\DistributeOrderProceedsListener@handle',
      2 => 'App\\Listeners\\StockAdjustmentListener@handleOrderPaid',
    ),
    'App\\Events\\OrderCancelled' => 
    array (
      0 => 'App\\Listeners\\ActivityLoggerSubscriber@handleOrderCancelled',
      1 => 'App\\Listeners\\StockAdjustmentListener@handleOrderCancelled',
    ),
    'App\\Events\\OrderRefunded' => 
    array (
      0 => 'App\\Listeners\\ActivityLoggerSubscriber@handleOrderRefunded',
      1 => 'App\\Listeners\\StockAdjustmentListener@handleOrderRefunded',
    ),
    'App\\Events\\PaymentWebhookReceived' => 
    array (
      0 => 'App\\Listeners\\HandlePaymentWebhook@handle',
    ),
  ),
);