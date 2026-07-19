import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PlanCuentasController::index
 * @see app/Http/Controllers/PlanCuentasController.php:11
 * @route '/plan-cuentas'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/plan-cuentas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PlanCuentasController::index
 * @see app/Http/Controllers/PlanCuentasController.php:11
 * @route '/plan-cuentas'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PlanCuentasController::index
 * @see app/Http/Controllers/PlanCuentasController.php:11
 * @route '/plan-cuentas'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PlanCuentasController::index
 * @see app/Http/Controllers/PlanCuentasController.php:11
 * @route '/plan-cuentas'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PlanCuentasController::index
 * @see app/Http/Controllers/PlanCuentasController.php:11
 * @route '/plan-cuentas'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PlanCuentasController::index
 * @see app/Http/Controllers/PlanCuentasController.php:11
 * @route '/plan-cuentas'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PlanCuentasController::index
 * @see app/Http/Controllers/PlanCuentasController.php:11
 * @route '/plan-cuentas'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\PlanCuentasController::storeSubcuenta
 * @see app/Http/Controllers/PlanCuentasController.php:20
 * @route '/plan-cuentas/subcuenta'
 */
export const storeSubcuenta = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeSubcuenta.url(options),
    method: 'post',
})

storeSubcuenta.definition = {
    methods: ["post"],
    url: '/plan-cuentas/subcuenta',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PlanCuentasController::storeSubcuenta
 * @see app/Http/Controllers/PlanCuentasController.php:20
 * @route '/plan-cuentas/subcuenta'
 */
storeSubcuenta.url = (options?: RouteQueryOptions) => {
    return storeSubcuenta.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PlanCuentasController::storeSubcuenta
 * @see app/Http/Controllers/PlanCuentasController.php:20
 * @route '/plan-cuentas/subcuenta'
 */
storeSubcuenta.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeSubcuenta.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\PlanCuentasController::storeSubcuenta
 * @see app/Http/Controllers/PlanCuentasController.php:20
 * @route '/plan-cuentas/subcuenta'
 */
    const storeSubcuentaForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeSubcuenta.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\PlanCuentasController::storeSubcuenta
 * @see app/Http/Controllers/PlanCuentasController.php:20
 * @route '/plan-cuentas/subcuenta'
 */
        storeSubcuentaForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeSubcuenta.url(options),
            method: 'post',
        })
    
    storeSubcuenta.form = storeSubcuentaForm
const planCuentas = {
    index: Object.assign(index, index),
storeSubcuenta: Object.assign(storeSubcuenta, storeSubcuenta),
}

export default planCuentas