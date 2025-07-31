import { SafeAreaView, ScrollView, StyleSheet } from "react-native"
import RegisterForm from "@/components/RegisterForm";

export default function App() {
  return (
    <SafeAreaView style={styles.safeArea}>
      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        <RegisterForm />
        {/* Your content goes here */}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#f0f0f0',
  },
  scrollViewContent: {
    flexGrow: 1,
    justifyContent: "center",
    paddingVertical: 20,
  },
});