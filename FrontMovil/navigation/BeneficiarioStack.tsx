import { createStackNavigator } from "@react-navigation/stack";
import BeneficiarioInicio from "../screens/BeneficiarioInicio";
import PerfilBeneficiario from "../screens/PerfilBeneficiario";
import EventosBeneficiario from "../screens/EventosBeneficiario";
import EntregasBeneficiario from "../screens/EntregasBeneficiario";
import AgregarEventoBeneficiario from "@/screens/AgregarEventoBeneficiario";

const Stack = createStackNavigator();

export default function BeneficiarioStack() {
return (
    <Stack.Navigator>
    <Stack.Screen name="InicioBeneficiario" component={BeneficiarioInicio} />
    <Stack.Screen name="PerfilBeneficiario" component={PerfilBeneficiario} />
    <Stack.Screen name="EventosBeneficiario" component={EventosBeneficiario} />
    <Stack.Screen name="EntregasBeneficiario" component={EntregasBeneficiario} />
    <Stack.Screen name="AgregarEventoBeneficiario" component={AgregarEventoBeneficiario} />
    </Stack.Navigator>
);
}
