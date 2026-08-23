# Code Citations

## License: unknown
https://github.com/bddimitrov/paynetics-task/tree/9f60a8b4213bfb9a90d475283ce8c87b73743b63/database/migrations/2023_10_03_151330_create_tasks_table.php

```
->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function
```


## License: unknown
https://github.com/Laravel-RootX/DB-Query-Builder/tree/f9e8a9fa82281926a077e3b55a15d79827989496/database/migrations/2020_06_25_035604_create_departments_table.php

```
Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
```


## License: unknown
https://github.com/AhnabShahin/hr/tree/41222bafeeb915c9ced4352230d6515e450534f8/src/Database/migrations/2023_08_20_053018_create_departments_table.php

```
\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table-
```


## License: unknown
https://github.com/avanegas/cms/tree/8f414453e9ca931cb3c44307fbfc9eaf75afd18b/database/migrations/2020_08_03_150532_create_locations_table.php

```
\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id
```


## License: unknown
https://github.com/flairxy/c_courier/tree/4853b2b3dfdd1aa9798a27295a2e99a7b10f45b0/database/migrations/2019_05_09_210214_create_shipments_table.php

```
->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'
```

