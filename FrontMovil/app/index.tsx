import { View, Text, StyleSheet, Image, TouchableOpacity } from "react-native" // Eliminamos ImageBackground
import { Link } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"

import FondoImage from "../assets/images/fondo.jpg"
import LogoImage from "../assets/images/logo.jpg"

export default function WelcomeScreen() {
  const primaryColor = "#6B3F2E"
  const buttonColor = "#8B4513"

  return (
    <View style={styles.containerWithBackground}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <View style={styles.overlay}>
        <View style={styles.topSection}>
          <Image source={LogoImage} style={styles.logo} resizeMode="contain" />
          <Text style={styles.slogan}>¡Cada alimento cuenta, cada persona importa!</Text>
          <Text style={styles.description}>
            Bienvenido a MACRA, un espacio donde tu ayuda se transforma en esperanza.
          </Text>
          <View style={styles.placeholderImageContainer}>
            <MaterialCommunityIcons name="image" size={80} color="#ccc" />
          </View>
        </View>

        <View style={styles.bottomSection}>
          <View style={styles.buttonContainer}>
            <Link href="/login" asChild>
              <TouchableOpacity style={[styles.button, { backgroundColor: buttonColor }]}>
                <Text style={styles.buttonText}>Iniciar Sesión</Text>
              </TouchableOpacity>
            </Link>
            <Link href="/register" asChild>
              <TouchableOpacity style={[styles.button, { backgroundColor: buttonColor }]}>
                <Text style={styles.buttonText}>Regístrate</Text>
              </TouchableOpacity>
            </Link>
          </View>

          <View style={styles.socialIcons}>
            <MaterialCommunityIcons name="facebook" size={30} color="white" style={styles.socialIcon} />
            <MaterialCommunityIcons name="instagram" size={30} color="white" style={styles.socialIcon} />
            <MaterialCommunityIcons name="twitter" size={30} color="white" style={styles.socialIcon} />
          </View>

          <Text style={styles.footerText}>© 2025 MACRA Banco de Alimentos</Text>
        </View>
      </View>
    </View>
  )
}

const styles = StyleSheet.create({
  containerWithBackground: {
    flex: 1,
  },
  backgroundImage: {
    position: "absolute",
    top: 0,
    left: 0,
    bottom: 0,
    right: 0,
    width: "100%",
    height: "100%",
  },
  overlay: {
    flex: 1,
    backgroundColor: "rgba(107, 63, 46, 0.9)",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 40,
    paddingHorizontal: 20,
  },
  topSection: {
    alignItems: "center",
    flex: 1,
    justifyContent: "center",
  },
  logo: {
    width: 150,
    height: 150,
    marginBottom: 20,
  },
  slogan: {
    fontSize: 22,
    fontWeight: "bold",
    color: "white",
    textAlign: "center",
    marginBottom: 10,
    fontStyle: "italic",
  },
  description: {
    fontSize: 16,
    color: "white",
    textAlign: "center",
    marginBottom: 30,
  },
  placeholderImageContainer: {
    width: "80%",
    height: 180,
    backgroundColor: "rgba(255, 255, 255, 0.2)",
    borderRadius: 10,
    justifyContent: "center",
    alignItems: "center",
    marginBottom: 30,
  },
  bottomSection: {
    width: "100%",
    alignItems: "center",
  },
  buttonContainer: {
    flexDirection: "row",
    justifyContent: "space-around",
    width: "100%",
    marginBottom: 30,
  },
  button: {
    paddingVertical: 12,
    paddingHorizontal: 30,
    borderRadius: 25,
    minWidth: 150,
    alignItems: "center",
  },
  buttonText: {
    color: "white",
    fontSize: 18,
    fontWeight: "bold",
  },
  socialIcons: {
    flexDirection: "row",
    marginBottom: 20,
  },
  socialIcon: {
    marginHorizontal: 15,
  },
  footerText: {
    color: "white",
    fontSize: 14,
  },
})
