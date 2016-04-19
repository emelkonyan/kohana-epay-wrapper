Kohana module for wrapping the ePay API.

First in config/epay-wrapper.php should be declared the four major values:
- api_base (the base URL to call, could be changed for production/developement);
- appid
- secret (these two are aquired trough the ePay sales office)
- deviceid

After that just create new the object and init it

    $my_adapter = Epay_Adapter();
    $my_adapter->init();

The adapter is ready

