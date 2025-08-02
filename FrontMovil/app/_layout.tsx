import { Stack } from "expo-router"
import { StatusBar } from "expo-status-bar"
import { SafeAreaProvider } from "react-native-safe-area-context"

export default function RootLayout() {
  return (
    <SafeAreaProvider>
      <StatusBar style="dark" />
      <Stack screenOptions={{ headerShown: false }}>
        <Stack.Screen name="index" /> {/* La pantalla de bienvenida */}
        <Stack.Screen name="login" /> {/* La pantalla de login */}
        <Stack.Screen name="register" /> {/* La pantalla de registro */}
        {/* Puedes añadir más pantallas aquí */}
      </Stack>
    </SafeAreaProvider>
  )
}
