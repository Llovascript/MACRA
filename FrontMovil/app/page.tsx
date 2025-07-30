import { View, StyleSheet } from "react-native"
import RegisterForm from "../components/RegisterForm"

export default function Page() {
  return (
    <View style={styles.container}>
      <RegisterForm />
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },
})
