import mods from "../factory/mods";
import users from "../factory/users";

export default function({$axios}, inject) {
    const Factories = {
        registered: {},

        registerFactory(name, factory) {
            this.registered[name] = factory($axios);
        },
    
        async getOne(name, ...props) {
            if (!this.registered[name]) {
                console.error(`Factory ${name} does not exist!`);
                return;
            }
            return await this.registered[name].getOne(...props);
        }
    }

    Factories.registerFactory("mods", mods)
    Factories.registerFactory("users", users)

    inject('factory', Factories);
}