# Directory Search
The Directory Search API requires an Apigee API key and approval from relevant data stewards. For more information requesting access, check out the documents on the [API Service Registry](https://apiserviceregistry.northwestern.edu).

Once you get your key, add it to the `.env` file as the `DIRECTORY_SEARCH_API_KEY` property. By default, the production service will be used, but you can define `DIRECTORY_SEARCH_URL` if you want to use dev or QA.

```php
<?php

namespace App\Http\Controllers;

use Northwestern\SysDev\SOA\DirectorySearch;

class MyController extends Controllers
{
    public function login(Request $request, DirectorySearch $directory)
    {
        // Defaults to the expanded version of the API call
        $info = $directory->lookupByNetId('nie7321');
        if ($info == false) {
            dd($directory->getLastError());
        }

        dd($info['mail']);

        // There are other lookup methods available. Anywhere 'basic' is specified, you may also use 'public' or 'expanded'.
        $info = $directory->lookupByNetId('nie7321', 'basic');

        $info = $directory->lookup('1234567', 'emplid', 'basic');
        $info = $directory->lookup('1234567', 'hremplid', 'basic');
        $info = $directory->lookup('234567', 'sesemplid', 'basic');
        $info = $directory->lookup('1234567', 'barcode', 'basic');
        $info = $directory->lookup('test@northwestern.edu', 'mail', 'basic');
        $info = $directory->lookup('test2020@u.northwestern.edu', 'studentemail', 'basic');
    }
}
```

Refer to the [Directory Search API docs](https://northwestern-apiportal.apigee.io/IDM-Services) for more information about what fields you will receive.
