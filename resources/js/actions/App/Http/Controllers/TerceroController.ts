import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/terceros',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
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
* @see \App\Http\Controllers\TerceroController::storeCliente
 * @see app/Http/Controllers/TerceroController.php:23
 * @route '/terceros/cliente'
 */
export const storeCliente = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCliente.url(options),
    method: 'post',
})

storeCliente.definition = {
    methods: ["post"],
    url: '/terceros/cliente',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TerceroController::storeCliente
 * @see app/Http/Controllers/TerceroController.php:23
 * @route '/terceros/cliente'
 */
storeCliente.url = (options?: RouteQueryOptions) => {
    return storeCliente.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::storeCliente
 * @see app/Http/Controllers/TerceroController.php:23
 * @route '/terceros/cliente'
 */
storeCliente.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCliente.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\TerceroController::storeCliente
 * @see app/Http/Controllers/TerceroController.php:23
 * @route '/terceros/cliente'
 */
    const storeClienteForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeCliente.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::storeCliente
 * @see app/Http/Controllers/TerceroController.php:23
 * @route '/terceros/cliente'
 */
        storeClienteForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeCliente.url(options),
            method: 'post',
        })
    
    storeCliente.form = storeClienteForm
/**
* @see \App\Http\Controllers\TerceroController::updateCliente
 * @see app/Http/Controllers/TerceroController.php:61
 * @route '/terceros/cliente/{id}'
 */
export const updateCliente = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCliente.url(args, options),
    method: 'put',
})

updateCliente.definition = {
    methods: ["put"],
    url: '/terceros/cliente/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\TerceroController::updateCliente
 * @see app/Http/Controllers/TerceroController.php:61
 * @route '/terceros/cliente/{id}'
 */
updateCliente.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return updateCliente.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::updateCliente
 * @see app/Http/Controllers/TerceroController.php:61
 * @route '/terceros/cliente/{id}'
 */
updateCliente.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCliente.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\TerceroController::updateCliente
 * @see app/Http/Controllers/TerceroController.php:61
 * @route '/terceros/cliente/{id}'
 */
    const updateClienteForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateCliente.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::updateCliente
 * @see app/Http/Controllers/TerceroController.php:61
 * @route '/terceros/cliente/{id}'
 */
        updateClienteForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateCliente.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateCliente.form = updateClienteForm
/**
* @see \App\Http\Controllers\TerceroController::toggleClienteEstado
 * @see app/Http/Controllers/TerceroController.php:99
 * @route '/terceros/cliente/{id}/toggle'
 */
export const toggleClienteEstado = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleClienteEstado.url(args, options),
    method: 'post',
})

toggleClienteEstado.definition = {
    methods: ["post"],
    url: '/terceros/cliente/{id}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TerceroController::toggleClienteEstado
 * @see app/Http/Controllers/TerceroController.php:99
 * @route '/terceros/cliente/{id}/toggle'
 */
toggleClienteEstado.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return toggleClienteEstado.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::toggleClienteEstado
 * @see app/Http/Controllers/TerceroController.php:99
 * @route '/terceros/cliente/{id}/toggle'
 */
toggleClienteEstado.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleClienteEstado.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\TerceroController::toggleClienteEstado
 * @see app/Http/Controllers/TerceroController.php:99
 * @route '/terceros/cliente/{id}/toggle'
 */
    const toggleClienteEstadoForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: toggleClienteEstado.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::toggleClienteEstado
 * @see app/Http/Controllers/TerceroController.php:99
 * @route '/terceros/cliente/{id}/toggle'
 */
        toggleClienteEstadoForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: toggleClienteEstado.url(args, options),
            method: 'post',
        })
    
    toggleClienteEstado.form = toggleClienteEstadoForm
/**
* @see \App\Http\Controllers\TerceroController::deleteCliente
 * @see app/Http/Controllers/TerceroController.php:170
 * @route '/terceros/cliente/{id}'
 */
export const deleteCliente = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCliente.url(args, options),
    method: 'delete',
})

deleteCliente.definition = {
    methods: ["delete"],
    url: '/terceros/cliente/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\TerceroController::deleteCliente
 * @see app/Http/Controllers/TerceroController.php:170
 * @route '/terceros/cliente/{id}'
 */
deleteCliente.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return deleteCliente.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::deleteCliente
 * @see app/Http/Controllers/TerceroController.php:170
 * @route '/terceros/cliente/{id}'
 */
deleteCliente.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCliente.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\TerceroController::deleteCliente
 * @see app/Http/Controllers/TerceroController.php:170
 * @route '/terceros/cliente/{id}'
 */
    const deleteClienteForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: deleteCliente.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::deleteCliente
 * @see app/Http/Controllers/TerceroController.php:170
 * @route '/terceros/cliente/{id}'
 */
        deleteClienteForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: deleteCliente.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    deleteCliente.form = deleteClienteForm
/**
* @see \App\Http\Controllers\TerceroController::storeProveedor
 * @see app/Http/Controllers/TerceroController.php:108
 * @route '/terceros/proveedor'
 */
export const storeProveedor = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProveedor.url(options),
    method: 'post',
})

storeProveedor.definition = {
    methods: ["post"],
    url: '/terceros/proveedor',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TerceroController::storeProveedor
 * @see app/Http/Controllers/TerceroController.php:108
 * @route '/terceros/proveedor'
 */
storeProveedor.url = (options?: RouteQueryOptions) => {
    return storeProveedor.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::storeProveedor
 * @see app/Http/Controllers/TerceroController.php:108
 * @route '/terceros/proveedor'
 */
storeProveedor.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProveedor.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\TerceroController::storeProveedor
 * @see app/Http/Controllers/TerceroController.php:108
 * @route '/terceros/proveedor'
 */
    const storeProveedorForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeProveedor.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::storeProveedor
 * @see app/Http/Controllers/TerceroController.php:108
 * @route '/terceros/proveedor'
 */
        storeProveedorForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeProveedor.url(options),
            method: 'post',
        })
    
    storeProveedor.form = storeProveedorForm
/**
* @see \App\Http\Controllers\TerceroController::updateProveedor
 * @see app/Http/Controllers/TerceroController.php:134
 * @route '/terceros/proveedor/{id}'
 */
export const updateProveedor = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProveedor.url(args, options),
    method: 'put',
})

updateProveedor.definition = {
    methods: ["put"],
    url: '/terceros/proveedor/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\TerceroController::updateProveedor
 * @see app/Http/Controllers/TerceroController.php:134
 * @route '/terceros/proveedor/{id}'
 */
updateProveedor.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return updateProveedor.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::updateProveedor
 * @see app/Http/Controllers/TerceroController.php:134
 * @route '/terceros/proveedor/{id}'
 */
updateProveedor.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProveedor.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\TerceroController::updateProveedor
 * @see app/Http/Controllers/TerceroController.php:134
 * @route '/terceros/proveedor/{id}'
 */
    const updateProveedorForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateProveedor.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::updateProveedor
 * @see app/Http/Controllers/TerceroController.php:134
 * @route '/terceros/proveedor/{id}'
 */
        updateProveedorForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateProveedor.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateProveedor.form = updateProveedorForm
/**
* @see \App\Http\Controllers\TerceroController::toggleProveedorEstado
 * @see app/Http/Controllers/TerceroController.php:161
 * @route '/terceros/proveedor/{id}/toggle'
 */
export const toggleProveedorEstado = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleProveedorEstado.url(args, options),
    method: 'post',
})

toggleProveedorEstado.definition = {
    methods: ["post"],
    url: '/terceros/proveedor/{id}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TerceroController::toggleProveedorEstado
 * @see app/Http/Controllers/TerceroController.php:161
 * @route '/terceros/proveedor/{id}/toggle'
 */
toggleProveedorEstado.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return toggleProveedorEstado.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::toggleProveedorEstado
 * @see app/Http/Controllers/TerceroController.php:161
 * @route '/terceros/proveedor/{id}/toggle'
 */
toggleProveedorEstado.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleProveedorEstado.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\TerceroController::toggleProveedorEstado
 * @see app/Http/Controllers/TerceroController.php:161
 * @route '/terceros/proveedor/{id}/toggle'
 */
    const toggleProveedorEstadoForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: toggleProveedorEstado.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::toggleProveedorEstado
 * @see app/Http/Controllers/TerceroController.php:161
 * @route '/terceros/proveedor/{id}/toggle'
 */
        toggleProveedorEstadoForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: toggleProveedorEstado.url(args, options),
            method: 'post',
        })
    
    toggleProveedorEstado.form = toggleProveedorEstadoForm
/**
* @see \App\Http\Controllers\TerceroController::deleteProveedor
 * @see app/Http/Controllers/TerceroController.php:185
 * @route '/terceros/proveedor/{id}'
 */
export const deleteProveedor = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteProveedor.url(args, options),
    method: 'delete',
})

deleteProveedor.definition = {
    methods: ["delete"],
    url: '/terceros/proveedor/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\TerceroController::deleteProveedor
 * @see app/Http/Controllers/TerceroController.php:185
 * @route '/terceros/proveedor/{id}'
 */
deleteProveedor.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return deleteProveedor.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::deleteProveedor
 * @see app/Http/Controllers/TerceroController.php:185
 * @route '/terceros/proveedor/{id}'
 */
deleteProveedor.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteProveedor.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\TerceroController::deleteProveedor
 * @see app/Http/Controllers/TerceroController.php:185
 * @route '/terceros/proveedor/{id}'
 */
    const deleteProveedorForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: deleteProveedor.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\TerceroController::deleteProveedor
 * @see app/Http/Controllers/TerceroController.php:185
 * @route '/terceros/proveedor/{id}'
 */
        deleteProveedorForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: deleteProveedor.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    deleteProveedor.form = deleteProveedorForm
const TerceroController = { index, storeCliente, updateCliente, toggleClienteEstado, deleteCliente, storeProveedor, updateProveedor, toggleProveedorEstado, deleteProveedor }

export default TerceroController