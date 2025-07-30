import { createStackNavigator } from "@react-navigation/stack";
import MenuDonante from "../screens/MenuDonante";
import AgregarDonante from "../screens/AgregarDonante";
import EliminarDonante from "../screens/EliminarDonante";
import ActualizarDonante from "../screens/ActualizarDonante";

const Stack = createStackNavigator();

export default function DonadorStack() {
    return (
    <Stack.Navigator>
    <Stack.Screen name="MenuDonante" component={MenuDonante} />
    <Stack.Screen name="AgregarDonante" component={AgregarDonante} />
    <Stack.Screen name="EliminarDonante" component={EliminarDonante} />
    <Stack.Screen name="ActualizarDonante" component={ActualizarDonante} />
    </Stack.Navigator>
    );
}
