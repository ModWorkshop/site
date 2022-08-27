import common from "./common";
import game from "./game";
import mod from "./mod";
import notifications from "./notifications";
import user from "./user";

export default {
    ...common,
    ...mod,
    ...game,
    ...notifications,
    ...user,
};