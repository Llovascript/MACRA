import { useState } from "react"
import { View, Text, TextInput, Button, Alert, StyleSheet, ActivityIndicator } from "react-native"
import { generateFastApiUrl } from "@/utils" // La ruta ahora es '../utils' porque está en 'components'

export default function RegisterForm() {
  const [username, setUsername] = useState("")
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [loading, setLoading] = useState(false)

  const handleRegister = async () => {
    if (!username || !email || !password) {
      Alert.alert("Error", "Por favor, completa todos los campos.")
      return
    }

    setLoading(true)
    try {
      // Asume que tu endpoint de registro en FastAPI es '/register'
      const apiUrl = generateFastApiUrl("/register")
      console.log("Sending registration data to:", apiUrl)

      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          username,
          email,
          password,
        }),
      })

      const data = await response.json()

      if (response.ok) {
        Alert.alert("Éxito", data.message || "Registro exitoso!")
        setUsername("")
        setEmail("")
        setPassword("")
      } else {
        Alert.alert("Error de registro", data.detail || "Ocurrió un error al registrarse.")
      }
    } catch (error: any) {
      console.error("Error al registrar:", error)
      Alert.alert("Error de conexión", "No se pudo conectar con el servidor. " + error.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <View style={styles.container}>
      <Text style={styles.header}>Registro de Usuario</Text>
      <TextInput
        style={styles.input}
        placeholder="Nombre de usuario"
        value={username}
        onChangeText={setUsername}
        autoCapitalize="none"
      />
      <TextInput
        style={styles.input}
        placeholder="Correo electrónico"
        value={email}
        onChangeText={setEmail}
        keyboardType="email-address"
        autoCapitalize="none"
      />
      <TextInput
        style={styles.input}
        placeholder="Contraseña"
        value={password}
        onChangeText={setPassword}
        secureTextEntry
      />
      <Button title={loading ? "Registrando..." : "Registrarse"} onPress={handleRegister} disabled={loading} />
      {loading && <ActivityIndicator size="small" color="#0000ff" style={styles.spinner} />}
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    padding: 20,
    backgroundColor: "#f0f0f0",
  },
  header: {
    fontSize: 24,
    fontWeight: "bold",
    marginBottom: 30,
    textAlign: "center",
    color: "#333",
  },
  input: {
    height: 50,
    borderColor: "#ccc",
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 15,
    marginBottom: 15,
    backgroundColor: "#fff",
    fontSize: 16,
  },
  spinner: {
    marginTop: 20,
  },
})
