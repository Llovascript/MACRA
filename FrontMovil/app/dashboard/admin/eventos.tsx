"use client"
import { View, Text, StyleSheet, Image, TouchableOpacity } from "react-native"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import { useRouter } from "expo-router"

import FondoImage from "../../../assets/images/fondo.jpg"

export default function EventosAdmin() {
  const router = useRouter()

  const menuOptions = [
    {
      title: "Agregar",
      icon: "plus-circle",
      color: "#4CAF50",
      onPress: () => router.push("/dashboard/admin/eventos/add"),
    },
    {
      title: "Actualizar",
      icon: "refresh-circle",
      color: "#2196F3",
      onPress: () => router.push("/dashboard/admin/eventos/update"),
    },
    {
      title: "Eliminar",
      icon: "delete-circle",
      color: "#f44336",
      onPress: () => router.push("/dashboard/admin/eventos/delete"),
    },
  ]

  return (
    <View style={styles.containerWithBackground}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <View style={styles.overlay}>
        <View style={styles.header}>
          <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
          </TouchableOpacity>
          <Text style={styles.title}>Administración de eventos</Text>
          <View style={{ width: 40 }} />
        </View>

        <View style={styles.menuContainer}>
          {menuOptions.map((option, index) => (
            <TouchableOpacity key={index} style={styles.menuOption} onPress={option.onPress}>
              <View style={[styles.iconContainer, { backgroundColor: option.color }]}>
                <MaterialCommunityIcons name={option.icon as any} size={40} color="white" />
              </View>
              <Text style={styles.optionText}>{option.title}</Text>
            </TouchableOpacity>
          ))}
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
    backgroundColor: "rgba(255, 255, 255, 0.9)",
    paddingTop: 60,
    paddingHorizontal: 20,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    marginBottom: 50,
  },
  backButton: {
    backgroundColor: "white",
    borderRadius: 20,
    padding: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  title: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#333",
  },
  menuContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    gap: 30,
  },
  menuOption: {
    backgroundColor: "white",
    borderRadius: 15,
    padding: 20,
    width: "80%",
    flexDirection: "row",
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  iconContainer: {
    borderRadius: 15,
    padding: 15,
    marginRight: 20,
  },
  optionText: {
    fontSize: 20,
    fontWeight: "600",
    color: "#333",
  },
})
