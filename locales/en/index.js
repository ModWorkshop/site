import common from "./common";
import game from "./game";
import mod from "./mod";
import moderation from "./moderation";
import notifications from "./notifications";
import user from "./user";

export default {
    ...common,
    ...mod,
    ...game,
    ...notifications,
    ...user,
    ...moderation,
};