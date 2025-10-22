<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Invoice;
use App\Policies\InvoicePolicy;

class AuthServiceProvider extends ServiceProvider
{
	protected $policies = [
		Invoice::class => InvoicePolicy::class,
	];

	public function boot()
	{
		$this->registerPolicies();
	}
}
