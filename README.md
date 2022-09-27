Laravel-Repository is CRUD wrapper for laravel application

### Table of Contents
- [Table of Contents](#table-of-contents)
- [Installation](#installation)
- [Create Basic Repository](#create-basic-repository)
- [Basic Usage](#basic-usage)
- [Listing Modifier](#listing-modifier)
    - [Pagination](#pagination)
      - [Required Pagination](#required-pagination)
      - [Optional Pagination](#optional-pagination)
    - [Filtering](#filtering)
    - [Sorting](#sorting)
    - [Relationship Loading](#relationship-loading)
- [Events](#events)

### Installation
```
composer require klinikpintar/laravel-repository
```
### Create Basic Repository
```php
<?php
namespace App\Repositories;
use App\Models\User as Model;
use KlinikPintar\RepositorySoftDelete;

class UserRepository extends RepositorySoftDelete
{
    /**
     * model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model = Model::class;

    /**
     * fillable data model.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'role'
    ];
}
```
### Basic Usage
```php
Create user controller
<?php
namespace App\Http\Controllers;
use App\Repositories\UserRepository;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * repository
     *
     * @var \App\Repositories\UserRepository
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    /**
     * get list of companies
     */
    public function getList(Request $request)
    {
        $collection = $this->repository->getList($request);

        return UserResource::collection($collection);
    }

    /**
     * get detail of companies
     */
    public function getDetail(Request $request, int $id)
    {
        $user = $this->repository->getDetail($request, $id);

        return new UserResource($user);
    }

    /**
     * create User
     */
    public function create(CreateRequest $request)
    {
        $user = $this->repository->create($request);

        return (new UserResource($user))->additional([
            'message' => 'User has been created'
        ]);
    }

    /**
     * update User
     */
    public function update(UpdateRequest $request, int $id)
    {
        $user = $this->repository->update($request, $id);

        return (new UserResource($user))->additional([
            'message' => 'User has been updated'
        ]);
    }

    /**
     * activate User
     */
    public function activate(Request $request, int $id)
    {
        $user = $this->repository->activate($request, $id);

        return (new UserResource($user))->additional([
            'message' => 'User has been activated'
        ]);
    }

    /**
     * inactive User
     */
    public function inactive(Request $request, int $id)
    {
        $user = $this->repository->inactive($request, $id);

        return (new UserResource($user))->additional([
            'message' => 'User has been inactived'
        ]);
    }

    /**
     * delete User
     */
    public function delete(Request $request, int $id)
    {
        $user = $this->repository->delete($request, $id);

        return (new UserResource($user))->additional([
            'message' => 'User has been deleted'
        ]);
    }

    /**
     * restore User
     */
    public function restore(Request $request, int $id)
    {
        $user = $this->repository->restore($request, $id);

        return (new UserResource($user))->additional([
            'message' => 'User has been restored'
        ]);
    }

    /**
     * forceDelete User
     */
    public function forceDelete(Request $request, int $id)
    {
        $user = $this->repository->forceDelete($request, $id);

        return (new UserResource($user))->additional([
            'message' => 'User has been force deleted'
        ]);
    }
}
```
Then create route
```php
Route::prefix('/user')->group(function () {
    Route::get('/', [UserController::class, 'getList'])->name('user.list');
    Route::get('/{id}', [UserController::class, 'getDetail'])->name('user.detail');
    Route::post('/', [UserController::class, 'create'])->name('user.create');
    Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
    Route::put('/{id}/activate', [UserController::class, 'activate'])->name('user.activate');
    Route::put('/{id}/inactive', [UserController::class, 'inactive'])->name('user.inactive');
    Route::pute('/{id}/restore', [UserController::class, 'restore'])->name('user.restore');
    Route::delete('/{id}', [UserController::class, 'delete'])->name('user.delete');
    Route::delete('/{id}', [UserController::class, 'forceDelete'])->name('user.force-delete');
});

```
### Listing Modifier
Listing modifier has 4 main feature
##### Pagination
When request to endpoint listing, response can be pagination or not depends on object behavior, by default it is enabled, but you can disabled by add property `$paginationable` to `false`
```php
    /**
     * paginationable.
     *
     * @var bool
     */
    protected $paginationable = false;
```
###### Required Pagination
When object data is large in database it should be paginated, so if you request to endpoint `/user` for example, it will return Laravel `LengthAwarePaginator` response.
###### Optional Pagination
When data in database is not too large, but better in paginated it called optional pagination. To use this feature just add property `$optionalPagination` to 'true' by default false
```php
    /**
     * optional pagination.
     *
     * @var bool
     */
    protected $optionalPagination = true;
```
so if request has query `page` or `limit` it will send paginated response, otherwise it will return array with wrapped by data. For example:
- `/user` will NOT Paginated
- `/user?page=1` Paginated
- `/user?limit=15` Paginated
- `/user?page=2&limit=10` Paginated

##### Filtering
filtering is very flexible you can define as much as you need.
```php
    /**
     * apply filter.
     */
    protected function applyFilter(Request $request, Builder &$builder): void
    {
        if ($request->query('role')) {
            $builder->whereRole($request->role);
        }
    }
```
so if you request qith `/user?role=admin` it will apply filter to bilder.
##### Sorting
Property `$sortable` is enabled by default
```php
    /**
     * sortable.
     *
     * @var bool
     */
    protected $sortable = true;

    /**
     * field allowed to sort.
     *
     * @var array
     */
    protected $sortAllowedFields = ['id'];

    /**
     * default sort field.
     *
     * @var string
     */
    protected $defaultSortField = null;
```
first define what fields can be allowed to sort, then set default sort field if needed. By default sort is Ascending but if you need list descending you can pass query `descending=true`.

##### Relationship Loading
Some time we need object relation to be loaded on request, to enabled this feature add props `$relationable` to `true`
```php
    /**
     * relationable.
     *
     * @var bool
     */
    protected $relationable = true;

    /**
     * field allowed to get relation.
     *
     * @var array
     */
    protected $relationAllowed = ['contact', 'parent', 'parent.contact'];

    /**
     * relation autoload.
     *
     * @var mixed
     */
    protected $relation = null;
```
then you can request with `user?with=contact,parent`
### Events
Event is usefull to define side effect of the process, you can use event in repository by adding method like:
```php
    /**
     * on User created
     * 
     * @var \Illuminate\Http\Request
     * @var \Illuminate\Database\Eloquent\Model
     */
    public function onCreated(CreateRequest $request, User $user): void
    {
        $user->contact->create([...]);
    }
```
This event will called after user created, Available events is:
- `onCreated` call on created
- `onSaved` call on created or updated
- `onUpdated` call on updated
- `onDeleted` call on deleted softDelete or not
- `onForceDeleted` call on force delete
- `onRestored` call on restored
- `onActivated` call on object activated if implemented
- `onInactivated` call on object inactivate if implemented
- `onStatusChanged` call on object activated or inactivate

