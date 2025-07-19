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
    errorElement.textContent = message
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
  const inputs = form.querySelectorAll("input, button")
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
    submitBtn.value = "Logging in..."

    // MÉTODO MEJORADO para obtener datos del formulario
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

// Manejar envío del formulario de registro
registerForm?.addEventListener("submit", async (e) => {
  e.preventDefault()
  hideMessages("register")

  const submitBtn = registerForm.querySelector('input[type="submit"]')
  const originalValue = submitBtn.value

  try {
    toggleFormState(registerForm, true)
    submitBtn.value = "Registering..."

    // MÉTODO MEJORADO para obtener datos del formulario
    const nameInput = document.getElementById("register-name")
    const emailInput = document.getElementById("register-email")
    const passwordInput = document.getElementById("register-password")
    const confirmInput = document.getElementById("register-confirm")

    const nombre = nameInput?.value?.trim() || ""
    const correo = emailInput?.value?.trim() || ""
    const contraseña = passwordInput?.value || ""
    const confirmar_contraseña = confirmInput?.value || ""

    console.log("Valores de registro obtenidos:", {
      nombre: nombre,
      correo: correo,
      contraseña: contraseña ? "***" : "(vacío)",
      confirmar_contraseña: confirmar_contraseña ? "***" : "(vacío)",
    })

    // Validación mejorada
    if (!nombre || nombre === "") {
      showError("register-error", "Por favor ingresa tu nombre completo")
      return
    }

    if (!correo || correo === "") {
      showError("register-error", "Por favor ingresa tu correo electrónico")
      return
    }

    if (!contraseña || contraseña === "") {
      showError("register-error", "Por favor ingresa una contraseña")
      return
    }

    if (!confirmar_contraseña || confirmar_contraseña === "") {
      showError("register-error", "Por favor confirma tu contraseña")
      return
    }

    // Validación básica de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(correo)) {
      showError("register-error", "Por favor ingresa un correo válido")
      return
    }

    // Validar que las contraseñas coincidan
    if (contraseña !== confirmar_contraseña) {
      showError("register-error", "Las contraseñas no coinciden")
      return
    }

    if (contraseña.length < 6) {
      showError("register-error", "La contraseña debe tener al menos 6 caracteres")
      return
    }

    console.log("Enviando petición de registro...")

    const response = await fetch("/auth/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
      },
      body: JSON.stringify({
        nombre: nombre,
        correo: correo,
        contraseña: contraseña,
        rol_id: 1,
      }),
    })

    const data = await response.json()
    console.log("Respuesta del servidor:", data)

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
        const firstError = Object.values(data.errors)[0]
        if (Array.isArray(firstError)) {
          errorMessage = firstError[0]
        } else {
          errorMessage = firstError
        }
      } else if (data.message) {
        errorMessage = data.message
      }

      showError("register-error", errorMessage)
    }
  } catch (error) {
    console.error("Error de conexión:", error)
    showError("register-error", "Error de conexión. Verifica que el servidor esté funcionando.")
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
})
