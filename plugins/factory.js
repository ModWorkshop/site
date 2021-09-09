import basicCrud from "../factory/basic-crud";

export default function({$axios}, inject) {
    const Factories = {
        registered: {},

        registerFactory(name, factory=null) {
            this.registered[name] = factory && factory($axios) || basicCrud(name, $axios);
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

    Factories.registerFactory("mods");
    Factories.registerFactory("users");
    Factories.registerFactory("categories");

    inject('factory', Factories);
}