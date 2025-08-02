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
  console.log("Cambiando a modo registro")
  container.classList.add("sign-up-mode")
})

sign_in_btn?.addEventListener("click", (e) => {
  e.preventDefault()
  console.log("Cambiando a modo login")
  container.classList.remove("sign-up-mode")
})

// Función para mostrar errores
function showError(elementId, message) {
  const errorElement = document.getElementById(elementId)
  if (errorElement) {
    // Asegurar que el message sea string
    let displayMessage = message
    if (typeof message === 'object') {
      displayMessage = JSON.stringify(message)
    }

    errorElement.textContent = displayMessage
    errorElement.style.display = "block"
    // Ocultar mensaje de éxito si existe
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
    // Ocultar mensaje de error si existe
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

// Manejar envío del formulario de login
loginForm?.addEventListener("submit", async (e) => {
  e.preventDefault()
  hideMessages("login")

  const submitBtn = loginForm.querySelector('input[type="submit"]')
  const originalValue = submitBtn.value

  try {
    toggleFormState(loginForm, true)
    submitBtn.value = "Iniciando sesión..."

    // Obtener datos del formulario
    const emailInput = document.getElementById("login-email")
    const passwordInput = document.getElementById("login-password")

    const correo = emailInput?.value?.trim() || ""
    const contraseña = passwordInput?.value || ""

    console.log("Valores obtenidos:", {
      correo: correo,
      contraseña: contraseña ? "***" : "(vacío)",
      emailElement: !!emailInput,
      passwordElement: !!passwordInput,
    })

    // Validación mejorada
    if (!correo || correo === "") {
      showError("login-error", "Por favor ingresa tu correo electrónico")
      return
    }

    if (!contraseña || contraseña === "") {
      showError("login-error", "Por favor ingresa tu contraseña")
      return
    }

    // Validación básica de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(correo)) {
      showError("login-error", "Por favor ingresa un correo válido")
      return
    }

    console.log("Enviando petición de login...")

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
    console.log("Respuesta del servidor:", data)

    if (response.ok && data.success) {
      showSuccess("login-success", "Login exitoso! Redirigiendo...")

      // Redirigir después de un breve delay
      setTimeout(() => {
        window.location.href = data.redirect_url || "/dashboard"
      }, 1500)
    } else {
      let errorMessage = "Error en el login"

      if (data.errors) {
        // Si hay errores de validación específicos
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

// Manejar envío del formulario de registro EXTENDIDO
registerForm?.addEventListener("submit", async (e) => {
  e.preventDefault()
  hideMessages("register")

  const submitBtn = registerForm.querySelector('input[type="submit"]')
  const originalValue = submitBtn.value

  try {
    toggleFormState(registerForm, true)
    submitBtn.value = "Registrando..."

    // Obtener datos del formulario extendido
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

    // DEBUG: Verificar elementos select
    console.log("DEBUG - Elementos select encontrados:")
    console.log("- tipoEntidadSelect:", !!tipoEntidadSelect, tipoEntidadSelect?.value)
    console.log("- profileSelect:", !!profileSelect, profileSelect?.value)

    // Recopilar todos los datos
    const formData = {
      nombre: nameInput?.value?.trim() || "",
      pagina_web: websiteInput?.value?.trim() || "",
      apellido_paterno: apellidoPaternoInput?.value?.trim() || "",
      correo: emailInput?.value?.trim() || "",
      apellido_materno: apellidoMaternoInput?.value?.trim() || "",
      contraseña: passwordInput?.value || "",
      edad: edadInput?.value || "",
      confirmar_contraseña: confirmInput?.value || "",
      telefono: telefonoInput?.value?.trim() || "",
      tipo_entidad: tipoEntidadSelect?.value || "",
      rfc: rfcInput?.value?.trim() || "",
      perfil: profileSelect?.value || "",
      rol_id: 1
    }

    console.log("DEBUG - Datos de registro obtenidos:")
    console.log("- nombre:", `'${formData.nombre}'`)
    console.log("- apellido_paterno:", `'${formData.apellido_paterno}'`)
    console.log("- correo:", `'${formData.correo}'`)
    console.log("- contraseña:", formData.contraseña ? `***${formData.contraseña.length}chars` : "(vacío)")
    console.log("- confirmar_contraseña:", formData.confirmar_contraseña ? `***${formData.confirmar_contraseña.length}chars` : "(vacío)")
    console.log("- tipo_entidad:", `'${formData.tipo_entidad}'`)
    console.log("- perfil:", `'${formData.perfil}'`)
    console.log("- edad:", `'${formData.edad}'`)
    console.log("- telefono:", `'${formData.telefono}'`)
    console.log("- rfc:", `'${formData.rfc}'`)

    // Validaciones mejoradas
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

    // Validación básica de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(formData.correo)) {
      showError("register-error", "Por favor ingresa un correo válido")
      return
    }

    // Validar que las contraseñas coincidan
    if (formData.contraseña !== formData.confirmar_contraseña) {
      showError("register-error", "Las contraseñas no coinciden")
      return
    }

    if (formData.contraseña.length < 6) {
      showError("register-error", "La contraseña debe tener al menos 6 caracteres")
      return
    }

    // Validar edad si se proporciona
    if (formData.edad && (formData.edad < 18 || formData.edad > 120)) {
      showError("register-error", "La edad debe estar entre 18 y 120 años")
      return
    }

    // Validar RFC si se proporciona
    if (formData.rfc && formData.rfc.length < 10) {
      showError("register-error", "El RFC debe tener al menos 10 caracteres")
      return
    }

    console.log("Enviando petición de registro...")

    // Preparar datos para el servidor (mapear nombres de campos)
    const serverData = {
      nombre: formData.nombre,
      apellido_paterno: formData.apellido_paterno,
      correo: formData.correo,
      contraseña: formData.contraseña,
      confirmar_contraseña: formData.confirmar_contraseña,
      perfil: formData.perfil,
      rol_id: formData.rol_id,

      // Mapear campos opcionales
      apellido_materno: formData.apellido_materno || undefined,
      pagina_web: formData.pagina_web || undefined,
      edad: formData.edad ? parseInt(formData.edad) : undefined,
      telefono: formData.telefono || undefined,
      rfc: formData.rfc || undefined,

      // Mapear tipo_entidad correctamente
      tipo: formData.tipo_entidad, // persona -> persona, organizacion -> organizacion
      tipo_entidad: formData.tipo_entidad // También para Laravel
    }

    // Remover campos undefined (pero mantener confirmar_contraseña)
    Object.keys(serverData).forEach(key => {
      if (serverData[key] === undefined || serverData[key] === '') {
        // No eliminar confirmar_contraseña aunque esté vacío
        if (key !== 'confirmar_contraseña') {
          delete serverData[key]
        }
      }
    })

    console.log("DEBUG - Datos para enviar al servidor:")
    console.log("- nombre:", `'${serverData.nombre}'`)
    console.log("- apellido_paterno:", `'${serverData.apellido_paterno}'`)
    console.log("- correo:", `'${serverData.correo}'`)
    console.log("- contraseña:", serverData.contraseña ? "***" : "(vacío)")
    console.log("- confirmar_contraseña:", serverData.confirmar_contraseña ? "***" : "(vacío)")
    console.log("- tipo (mapeado):", `'${serverData.tipo}'`)
    console.log("- perfil:", `'${serverData.perfil}'`)
    if (serverData.edad) console.log("- edad:", serverData.edad)
    if (serverData.telefono) console.log("- telefono:", `'${serverData.telefono}'`)
    if (serverData.rfc) console.log("- rfc:", `'${serverData.rfc}'`)

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
    console.log("DEBUG - Respuesta del servidor:", data)

    if (response.ok && data.success) {
      showSuccess("register-success", "Registro exitoso! Puedes iniciar sesión ahora.")

      // Limpiar formulario
      registerForm.reset()

      // Cambiar al formulario de login después de un delay
      setTimeout(() => {
        container.classList.remove("sign-up-mode")
      }, 2000)
    } else {
      let errorMessage = "Error al registrar usuario"

      if (data.errors) {
        // Si hay errores de validación específicos
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

      console.log("DEBUG - Error data:", data)
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

// Debug: Verificar valores de los campos en tiempo real
document.addEventListener("DOMContentLoaded", () => {
  const emailInput = document.getElementById("login-email")
  const passwordInput = document.getElementById("login-password")

  if (emailInput) {
    emailInput.addEventListener("input", () => {
      console.log("Email value:", emailInput.value)
    })
  }

  if (passwordInput) {
    passwordInput.addEventListener("input", () => {
      console.log("Password length:", passwordInput.value.length)
    })
  }

  // Validación en tiempo real para el RFC
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

  // Validación en tiempo real para teléfono
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