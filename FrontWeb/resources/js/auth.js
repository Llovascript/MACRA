import "./bootstrap"

// Elementos del DOM
const sign_in_btn = document.querySelector("#sign-in-btn")
const sign_up_btn = document.querySelector("#sign-up-btn")
const container = document.querySelector(".container")
const loginForm = document.querySelector("#loginForm")
const registerForm = document.querySelector("#registerForm")

console.log("Auth.js cargado correctamente")

// Verificar que los elementos existen
if (!sign_in_btn || !sign_up_btn || !container) {
  console.error("Elementos del DOM no encontrados")
} else {
  console.log("Todos los elementos del DOM encontrados")
}

// Animaciones del formulario
sign_up_btn?.addEventListener("click", (e) => {
  e.preventDefault()
  container.classList.add("sign-up-mode")
})

sign_in_btn?.addEventListener("click", (e) => {
  e.preventDefault()
  container.classList.remove("sign-up-mode")
})

// Función para mostrar errores
function showError(elementId, message) {
  const errorElement = document.getElementById(elementId)
  if (errorElement) {
    let displayMessage = message
    if (typeof message === 'object') {
      displayMessage = JSON.stringify(message)
    }

    errorElement.textContent = displayMessage
    errorElement.style.display = "block"

    const successElement = document.getElementById(elementId.replace("error", "success"))
    if (successElement) {
      successElement.style.display = "none"
    }
  }
}

// Función para mostrar éxito
function showSuccess(elementId, message) {
  const successElement = document.getElementById(elementId)
  if (successElement) {
    successElement.textContent = message
    successElement.style.display = "block"
    const errorElement = document.getElementById(elementId.replace("success", "error"))
    if (errorElement) {
      errorElement.style.display = "none"
    }
  }
}

// Función para ocultar mensajes
function hideMessages(prefix) {
  const errorElement = document.getElementById(`${prefix}-error`)
  const successElement = document.getElementById(`${prefix}-success`)
  if (errorElement) errorElement.style.display = "none"
  if (successElement) successElement.style.display = "none"
}

// Función para deshabilitar/habilitar formulario
function toggleFormState(form, disabled) {
  const inputs = form.querySelectorAll("input, button, select")
  inputs.forEach((input) => {
    input.disabled = disabled
  })
}

// Función para determinar el mensaje de redirección basado en el rol
function getRoleRedirectMessage(userRole) {
  switch (userRole) {
    case 1:
      return "Login exitoso! Redirigiendo al panel de administrador..."
    case 2:
      return "Login exitoso! Redirigiendo al menú de donantes..."
    case 3:
      return "Login exitoso! Redirigiendo al menú de beneficiarios..."
    default:
      return "Login exitoso! Redirigiendo..."
  }
}

// Manejar envío del formulario de login
loginForm?.addEventListener("submit", async (e) => {
  e.preventDefault()
  hideMessages("login")

  const submitBtn = loginForm.querySelector('input[type="submit"]')
  const originalValue = submitBtn.value

  try {
    toggleFormState(loginForm, true)
    submitBtn.value = "Iniciando sesión..."

    const emailInput = document.getElementById("login-email")
    const passwordInput = document.getElementById("login-password")

    const correo = emailInput?.value?.trim() || ""
    const contraseña = passwordInput?.value || ""

    // Validaciones
    if (!correo || correo === "") {
      showError("login-error", "Por favor ingresa tu correo electrónico")
      return
    }

    if (!contraseña || contraseña === "") {
      showError("login-error", "Por favor ingresa tu contraseña")
      return
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(correo)) {
      showError("login-error", "Por favor ingresa un correo válido")
      return
    }

    const response = await fetch("/auth/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
      body: JSON.stringify({
        correo: correo,
        contraseña: contraseña,
      }),
    })

    const data = await response.json()

    if (response.ok && data.success) {
      const userRole = data.user_role
      const redirectMessage = getRoleRedirectMessage(userRole)

      showSuccess("login-success", redirectMessage)

      setTimeout(() => {
        const redirectUrl = data.redirect_url || "/dashboard"
        window.location.href = redirectUrl
      }, 2000)
    } else {
      let errorMessage = "Error en el login"

      if (data.errors) {
        const firstError = Object.values(data.errors)[0]
        if (Array.isArray(firstError)) {
          errorMessage = firstError[0]
        } else {
          errorMessage = firstError
        }
      } else if (data.message) {
        errorMessage = data.message
      }

      showError("login-error", errorMessage)
    }
  } catch (error) {
    console.error("Error de conexión:", error)
    showError("login-error", "Error de conexión. Verifica que el servidor esté funcionando.")
  } finally {
    toggleFormState(loginForm, false)
    submitBtn.value = originalValue
  }
})

// Manejar envío del formulario de registro
registerForm?.addEventListener("submit", async (e) => {
  e.preventDefault()
  hideMessages("register")

  const submitBtn = registerForm.querySelector('input[type="submit"]')
  const originalValue = submitBtn.value

  try {
    toggleFormState(registerForm, true)
    submitBtn.value = "Registrando..."

    // Obtener datos del formulario
    const nameInput = document.getElementById("register-name")
    const websiteInput = document.getElementById("register-website")
    const apellidoPaternoInput = document.getElementById("register-apellido-paterno")
    const emailInput = document.getElementById("register-email")
    const apellidoMaternoInput = document.getElementById("register-apellido-materno")
    const passwordInput = document.getElementById("register-password")
    const edadInput = document.getElementById("register-edad")
    const confirmInput = document.getElementById("register-confirm")
    const telefonoInput = document.getElementById("register-telefono")
    const tipoEntidadSelect = document.getElementById("register-tipo-entidad")
    const rfcInput = document.getElementById("register-rfc")
    const profileSelect = document.getElementById("register-profile")

    // Función para mapear perfil a rol_id
    function mapPerfilToRolId(perfil) {
      const perfilLower = perfil?.toLowerCase()?.trim()

      switch (perfilLower) {
        case 'donante':
          return 2
        case 'beneficiario':
          return 3
        default:
          return 1
      }
    }

    // Recopilar datos
    const perfilSeleccionado = profileSelect?.value || ""
    const rolIdMapeado = mapPerfilToRolId(perfilSeleccionado)

    const formData = {
      nombre: nameInput?.value?.trim() || "",
      apellido_paterno: apellidoPaternoInput?.value?.trim() || "",
      apellido_materno: apellidoMaternoInput?.value?.trim() || "",
      correo: emailInput?.value?.trim() || "",
      contraseña: passwordInput?.value || "",
      confirmar_contraseña: confirmInput?.value || "",
      tipo_entidad: tipoEntidadSelect?.value || "",
      perfil: perfilSeleccionado,

      // Campos opcionales
      pagina_web: websiteInput?.value?.trim() || "",
      edad: edadInput?.value || "",
      telefono: telefonoInput?.value?.trim() || "",
      rfc: rfcInput?.value?.trim() || ""
    }

    // Validaciones
    if (!formData.nombre) {
      showError("register-error", "Por favor ingresa tu nombre")
      return
    }

    if (!formData.apellido_paterno) {
      showError("register-error", "Por favor ingresa tu apellido paterno")
      return
    }

    if (!formData.correo) {
      showError("register-error", "Por favor ingresa tu correo electrónico")
      return
    }

    if (!formData.contraseña) {
      showError("register-error", "Por favor ingresa una contraseña")
      return
    }

    if (!formData.confirmar_contraseña) {
      showError("register-error", "Por favor confirma tu contraseña")
      return
    }

    if (!formData.tipo_entidad) {
      showError("register-error", "Por favor selecciona el tipo de entidad")
      return
    }

    if (!formData.perfil) {
      showError("register-error", "Por favor selecciona tu tipo de perfil")
      return
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(formData.correo)) {
      showError("register-error", "Por favor ingresa un correo válido")
      return
    }

    if (formData.contraseña !== formData.confirmar_contraseña) {
      showError("register-error", "Las contraseñas no coinciden")
      return
    }

    if (formData.contraseña.length < 6) {
      showError("register-error", "La contraseña debe tener al menos 6 caracteres")
      return
    }

    if (formData.edad && (formData.edad < 18 || formData.edad > 120)) {
      showError("register-error", "La edad debe estar entre 18 y 120 años")
      return
    }

    if (formData.rfc && formData.rfc.length < 10) {
      showError("register-error", "El RFC debe tener al menos 10 caracteres")
      return
    }

    // Preparar datos para el servidor
    const serverData = {
      nombre: formData.nombre,
      apellido_paterno: formData.apellido_paterno,
      correo: formData.correo,
      contraseña: formData.contraseña,
      confirmar_contraseña: formData.confirmar_contraseña,
      perfil: formData.perfil,
      tipo_entidad: formData.tipo_entidad,

      // Campos opcionales
      apellido_materno: formData.apellido_materno || undefined,
      pagina_web: formData.pagina_web || undefined,
      edad: formData.edad ? parseInt(formData.edad) : undefined,
      telefono: formData.telefono || undefined,
      rfc: formData.rfc || undefined
    }

    // Remover campos undefined
    Object.keys(serverData).forEach(key => {
      if (serverData[key] === undefined || serverData[key] === '') {
        if (key !== 'confirmar_contraseña') {
          delete serverData[key]
        }
      }
    })

    const response = await fetch("/auth/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
      body: JSON.stringify(serverData)
    })

    const data = await response.json()

    if (response.ok && data.success) {
      const profileMessage = formData.perfil === 'donante'
        ? "Registro exitoso como donante! Puedes iniciar sesión ahora."
        : "Registro exitoso como beneficiario! Puedes iniciar sesión ahora."

      showSuccess("register-success", profileMessage)

      // Limpiar formulario
      registerForm.reset()

      // Cambiar al formulario de login
      setTimeout(() => {
        container.classList.remove("sign-up-mode")
      }, 2000)
    } else {
      let errorMessage = "Error al registrar usuario"

      if (data.errors) {
        const errors = data.errors
        const firstField = Object.keys(errors)[0]
        const firstError = errors[firstField]

        if (Array.isArray(firstError)) {
          errorMessage = firstError[0]
        } else {
          errorMessage = firstError
        }
      } else if (data.message) {
        errorMessage = data.message
      } else if (typeof data === 'string') {
        errorMessage = data
      }

      showError("register-error", errorMessage)
    }
  } catch (error) {
    console.error("Error de conexión:", error)
    let errorMessage = "Error de conexión. Verifica que el servidor esté funcionando."

    if (error.message) {
      errorMessage = error.message
    }

    showError("register-error", errorMessage)
  } finally {
    toggleFormState(registerForm, false)
    submitBtn.value = originalValue
  }
})

// Validaciones en tiempo real
document.addEventListener("DOMContentLoaded", () => {
  // RFC: Solo letras y números, máximo 13 caracteres
  const rfcInput = document.getElementById("register-rfc")
  if (rfcInput) {
    rfcInput.addEventListener("input", (e) => {
      let value = e.target.value.replace(/[^A-Za-z0-9]/g, "").toUpperCase()
      if (value.length > 13) {
        value = value.slice(0, 13)
      }
      e.target.value = value
    })
  }

  // Teléfono: Solo números, máximo 10 dígitos
  const telefonoInput = document.getElementById("register-telefono")
  if (telefonoInput) {
    telefonoInput.addEventListener("input", (e) => {
      let value = e.target.value.replace(/[^0-9]/g, "")
      if (value.length > 10) {
        value = value.slice(0, 10)
      }
      e.target.value = value
    })
  }
})