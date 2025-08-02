"use client"
import { View, Text, StyleSheet, TouchableOpacity, SafeAreaView } from "react-native"
import { useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"

export default function AdminBeneficiarios() {
  const router = useRouter()

  const menuItems = [
    {
      title: "Agregar",
      icon: "plus",
      color: "#4CAF50",
      onPress: () => console.log("Agregar beneficiario pressed"),
    },
    {
      title: "Actualizar",
      icon: "refresh",
      color: "#2196F3",
      onPress: () => console.log("Actualizar beneficiario pressed"),
    },
    {
      title: "Eliminar",
      icon: "delete",
      color: "#F44336",
      onPress: () => console.log("Eliminar beneficiario pressed"),
    },
  ]

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
        </TouchableOpacity>
        <Text style={styles.title}>Administración de{"\n"}beneficiarios</Text>
      </View>

      <View style={styles.menuContainer}>
        {menuItems.map((item, index) => (
          <TouchableOpacity key={index} style={styles.menuItem} onPress={item.onPress}>
            <View style={[styles.iconContainer, { backgroundColor: item.color }]}>
              <MaterialCommunityIcons name={item.icon as any} size={32} color="#fff" />
            </View>
            <Text style={styles.menuText}>{item.title}</Text>
          </TouchableOpacity>
        ))}
      </View>
    </SafeAreaView>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f5f5f5",
  },
  header: {
    backgroundColor: "#fff",
    paddingTop: 60,
    paddingBottom: 20,
    paddingHorizontal: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3.84,
    elevation: 5,
  },
  backButton: {
    marginBottom: 15,
  },
  title: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#333",
    lineHeight: 30,
  },
  menuContainer: {
    flex: 1,
    paddingHorizontal: 20,
    paddingTop: 30,
  },
  menuItem: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 20,
    marginBottom: 15,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3.84,
    elevation: 3,
    borderWidth: 1,
    borderColor: "#e0e0e0",
  },
  iconContainer: {
    width: 60,
    height: 60,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 20,
  },
  menuText: {
    fontSize: 18,
    fontWeight: "600",
    color: "#333",
    flex: 1,
  },
})
