import TerceroController from './TerceroController'
import ProductoController from './ProductoController'
import CompraController from './CompraController'
import VentaController from './VentaController'
import CajaController from './CajaController'
import PlanCuentasController from './PlanCuentasController'
import ContabilidadController from './ContabilidadController'
import UsuarioController from './UsuarioController'
import Settings from './Settings'
const Controllers = {
    TerceroController: Object.assign(TerceroController, TerceroController),
ProductoController: Object.assign(ProductoController, ProductoController),
CompraController: Object.assign(CompraController, CompraController),
VentaController: Object.assign(VentaController, VentaController),
CajaController: Object.assign(CajaController, CajaController),
PlanCuentasController: Object.assign(PlanCuentasController, PlanCuentasController),
ContabilidadController: Object.assign(ContabilidadController, ContabilidadController),
UsuarioController: Object.assign(UsuarioController, UsuarioController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers