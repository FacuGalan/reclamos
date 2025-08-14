<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Area;
use App\Models\UserRol;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AbmUsuarios extends Component
{
    use WithPagination;

    // Propiedades para filtros
    public $busqueda = '';
    public $filtro_rol = '';
    public $filtro_area = '';
    
    // Propiedades para modales
    public $selectedUsuarioId = null;
    public $showDeleteModal = false;
    public $showFormModal = false;
    public $isEditing = false;
    public $selectedUsuario = null;
    
    // Datos para los selects
    public $roles = [];
    public $areas = [];
    public $userAreas = []; // Áreas del usuario autenticado
    public $puedeVerTodos = false; // Nueva propiedad para saber si puede ver todos
    public $ver_privada = false; // Nueva propiedad para controlar acceso a áreas privadas

    // Datos del formulario
    public $dni = '';
    public $name = '';
    public $email = '';
    public $telefono = '';
    public $rol_id = '';
    public $password = '';
    public $password_confirmation = '';
    public $areas_asignadas = []; // Array de IDs de áreas asignadas

    // Estado de guardado
    public $isSaving = false;
    public $mostrarNotificacion = false;
    public $mensajeNotificacion = '';
    public $tipoNotificacion = 'success';
    public $notificacionTimestamp = null;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'filtro_rol' => ['except' => ''],
        'filtro_area' => ['except' => ''],
    ];

    protected $rules = [
        'dni' => 'required|numeric|digits_between:7,11|unique:users,dni',
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255|unique:users,email',
        'telefono' => 'required|numeric|digits_between:10,15',
        'rol_id' => 'nullable|exists:user_rols,id',
        'password' => 'required|string|min:8|confirmed',
        'areas_asignadas' => 'nullable|array',
    ];

    protected $messages = [
        'dni.required' => 'El DNI es obligatorio',
        'dni.numeric' => 'El DNI debe contener solo números',
        'dni.digits_between' => 'El DNI debe tener entre 7 y 11 dígitos',
        'dni.unique' => 'Ya existe un usuario con este DNI',
        'name.required' => 'El nombre es obligatorio',
        'email.email' => 'Ingrese un email válido',
        'email.unique' => 'Ya existe un usuario con este email',
        'telefono.required' => 'El teléfono es obligatorio',
        'telefono.digits_between' => 'El teléfono debe tener entre 10 y 15 dígitos',
        'password.required' => 'La contraseña es obligatoria',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        'password.confirmed' => 'Las contraseñas no coinciden',
    ];

    public function mount()
    {
        // Obtener las áreas del usuario logueado
        $usuarioActual = Auth::user();
        $this->userAreas = $usuarioActual->areas->pluck('id')->toArray();

        // CAMBIO PRINCIPAL: Verificar si el usuario puede ver todos los usuarios
        $this->puedeVerTodos = empty($this->userAreas);

        // Cargar datos para los selects
        $this->roles = UserRol::where('id', '>', Auth::user()->rol_id)->orderBy('nombre')->get();

        $this->ver_privada = Auth::user()->ver_privada;
        
        // CAMBIO: Si puede ver todos, cargar todas las áreas; si no, solo las suyas
        if ($this->puedeVerTodos) {
            $this->areas = Area::orderBy('nombre')->get();
        } else {
            $this->areas = Area::whereIn('id', $this->userAreas)->orderBy('nombre')->get();
        }
    }

    public function placeholder()
    {
        return view('livewire.placeholders.skeleton');
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingFiltroRol()
    {
        $this->resetPage();
    }

    public function updatingFiltroArea()
    {
        $this->resetPage();
    }

    public function getUsuarios()
    {
        $query = User::with(['areas', 'rol'])
            ->orderBy('name');

        // CAMBIO PRINCIPAL: Solo filtrar por áreas si el usuario NO puede ver todos
        if (!$this->puedeVerTodos) {
            $query->whereHas('areas', function($q) {
                $q->whereIn('areas.id', $this->userAreas);
            });
        }

        // Aplicar filtros
        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('email', 'like', '%' . $this->busqueda . '%')
                  ->orWhere('dni', 'like', '%' . $this->busqueda . '%');
            });
        }

        if ($this->filtro_rol) {
            $query->where('rol_id', $this->filtro_rol);
        }

        if ($this->filtro_area) {
            // CAMBIO: Verificar permisos solo si NO puede ver todos
            if ($this->puedeVerTodos || in_array($this->filtro_area, $this->userAreas)) {
                $query->whereHas('areas', function($q) {
                    $q->where('areas.id', $this->filtro_area);
                });
            }
        }

        return $query->paginate(15);
    }

    public function limpiarFiltros()
    {
        $this->busqueda = '';
        $this->filtro_rol = '';
        $this->filtro_area = '';
        $this->resetPage();
    }

    // Métodos para modales
    public function nuevoUsuario()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->selectedUsuarioId = null;
        $this->showFormModal = true;
    }

    public function editarUsuario($usuarioId)
    {
        $usuario = User::with('areas')->find($usuarioId);
        if (!$usuario) {
            $this->mostrarNotificacionError('El usuario solicitado no existe.');
            return;
        }

        // CAMBIO: Verificar permisos solo si NO puede ver todos
        if (!$this->puedeVerTodos) {
            $areasComunes = $usuario->areas->pluck('id')->intersect($this->userAreas);
            if ($areasComunes->isEmpty()) {
                $this->mostrarNotificacionError('No tienes permisos para editar este usuario.');
                return;
            }
        }

        $this->selectedUsuarioId = $usuarioId;
        $this->isEditing = true;
        $this->cargarDatosUsuario($usuario);
        $this->showFormModal = true;
    }

    public function cerrarModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->selectedUsuarioId = null;
        $this->isEditing = false;
    }

    public function cargarDatosUsuario($usuario)
    {
        $this->dni = $usuario->dni;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->telefono = $usuario->telefono;
        $this->rol_id = $usuario->rol_id;
        $this->areas_asignadas = $usuario->areas->pluck('id')->toArray();
    }

    public function resetForm()
    {
        $this->dni = '';
        $this->name = '';
        $this->email = '';
        $this->telefono = '';
        $this->rol_id = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->areas_asignadas = [];
        $this->isSaving = false;
        $this->resetErrorBag();
    }

    public function confirmarEliminacion($usuarioId)
    {
        $usuario = User::with('areas')->find($usuarioId);
        if (!$usuario) {
            $this->mostrarNotificacionError('El usuario solicitado no existe.');
            return;
        }

        // CAMBIO: Verificar permisos solo si NO puede ver todos
        if (!$this->puedeVerTodos) {
            $areasComunes = $usuario->areas->pluck('id')->intersect($this->userAreas);
            if ($areasComunes->isEmpty()) {
                $this->mostrarNotificacionError('No tienes permisos para eliminar este usuario.');
                return;
            }
        }

        $this->selectedUsuario = $usuario;
        $this->showDeleteModal = true;
    }

    public function eliminarUsuario()
    {
        if ($this->selectedUsuario) {
            try {
                // Verificar que no se esté intentando eliminar a sí mismo
                if ($this->selectedUsuario->id === Auth::id()) {
                    $this->mostrarNotificacionError('No puedes eliminar tu propio usuario.');
                    $this->showDeleteModal = false;
                    $this->selectedUsuario = null;
                    return;
                }

                $this->selectedUsuario->delete();
                $this->showDeleteModal = false;
                $this->selectedUsuario = null;
                
                $this->mostrarNotificacionExito('Usuario eliminado exitosamente');
            } catch (\Exception $e) {
                $this->mostrarNotificacionError('Error al eliminar el usuario: ' . $e->getMessage());
            }
        }
    }

    public function cerrarModalEliminacion()
    {
        $this->showDeleteModal = false;
        $this->selectedUsuario = null;
    }

    public function save()
    {
        $this->isSaving = true;

        try {
            // CAMBIO: Filtrar áreas solo si NO puede ver todos
            if (!$this->puedeVerTodos) {
                $this->areas_asignadas = array_intersect($this->areas_asignadas, $this->userAreas);
            }

            if ($this->isEditing) {
                // Actualizar reglas para edición
                $this->rules['dni'] = 'required|numeric|digits_between:7,11|unique:users,dni,' . $this->selectedUsuarioId;
                $this->rules['email'] = 'required|email|max:255|unique:users,email,' . $this->selectedUsuarioId;
                $this->rules['password'] = 'nullable|string|min:8|confirmed';
            }

            $this->validate();

            if (!$this->isEditing) {
                // Crear nuevo usuario
                $usuario = User::create([
                    'dni' => $this->dni,
                    'name' => $this->name,
                    'email' => $this->email,
                    'telefono' => $this->telefono,
                    'rol_id' => $this->rol_id ?: null,
                    'password' => Hash::make($this->password),
                    'ver_privada' => $this->ver_privada ? 1 : 0,
                ]);

                // Sincronizar áreas
                $usuario->areas()->sync($this->areas_asignadas);
                
                $mensaje = 'Usuario creado exitosamente';
            } else {
                // Actualizar usuario existente
                $usuario = User::find($this->selectedUsuarioId);
                
                $datosActualizacion = [
                    'dni' => $this->dni,
                    'name' => $this->name,
                    'email' => $this->email,
                    'telefono' => $this->telefono,
                    'rol_id' => $this->rol_id ?: null,
                    'ver_privada' => $this->ver_privada ? 1 : 0,
                ];

                // Solo actualizar la contraseña si se proporcionó una nueva
                if (!empty($this->password)) {
                    $datosActualizacion['password'] = Hash::make($this->password);
                }

                $usuario->update($datosActualizacion);

                // CAMBIO: Sincronizar áreas según permisos
                if ($this->puedeVerTodos) {
                    // Si puede ver todos, puede asignar cualquier área
                    $usuario->areas()->sync($this->areas_asignadas);
                } else {
                    // Si no puede ver todos, mantener áreas no gestionables
                    $areasActuales = $usuario->areas->pluck('id')->toArray();
                    $areasNoGestionables = array_diff($areasActuales, $this->userAreas);
                    $areasFinales = array_merge($this->areas_asignadas, $areasNoGestionables);
                    $usuario->areas()->sync($areasFinales);
                }
                
                $mensaje = 'Usuario actualizado exitosamente';
            }

            $this->mostrarNotificacionExito($mensaje);
            $this->cerrarModal();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // CLAVE: NO hagas return aquí, vuelve a lanzar la excepción
            $this->isSaving = false;
            throw $e; // ← ESTO es lo que faltaba
            
        } catch (\Exception $e) {
            $this->mostrarNotificacionError('Error al guardar el usuario: ' . $e->getMessage());
        }

        $this->isSaving = false;
    }

    public function mostrarNotificacionExito($mensaje = 'Operación realizada exitosamente')
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'success';
        $this->notificacionTimestamp = microtime(true);
    }

    public function mostrarNotificacionError($mensaje)
    {
        $this->mostrarNotificacion = true;
        $this->mensajeNotificacion = $mensaje;
        $this->tipoNotificacion = 'error';
        $this->notificacionTimestamp = microtime(true);
    }

    public function render()
    {
        $usuarios = $this->getUsuarios();
        
        return view('livewire.abm-usuarios', [
            'usuarios' => $usuarios
        ]);
    }
}