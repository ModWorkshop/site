import mods from "../factory/mods";
import users from "../factory/users";

export default function({$axios}, inject) {
    const Factories = {
        registered: {},

        registerFactory(name, factory) {
            this.registered[name] = factory($axios);
        },
    
        async doAction(name, action, ...props) {
            if (!this.registered[name]) {
                console.error(`Factory ${name} does not exist!`);
                return;
            }

            return await this.registered[name][action](...props);
        },

        async get(name, ...props) {
            return Factories.doAction(name, 'get', ...props);
        },

        async getOne(name, ...props) {
            return Factories.doAction(name, 'getOne', ...props);
        },

        async update(name, ...props) {
            return Factories.doAction(name, 'update', ...props);
        },

        async create(name, ...props) {
            return Factories.doAction(name, 'create', ...props);
        },

        async delete(name, ...props) {
            return Factories.doAction(name, 'delete', ...props);
        },
    };

    Factories.registerFactory("mods", mods);
    Factories.registerFactory("users", users);

    inject('factory', Factories);
}