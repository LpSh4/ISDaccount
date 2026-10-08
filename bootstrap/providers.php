<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    SergiX44\Nutgram\NutgramServiceProvider::class,
    //Ill come back to module architecture, but later. Leave it here for now if smth hapens
//    Modules\Account\Providers\AccountServiceProvider::class,
];
