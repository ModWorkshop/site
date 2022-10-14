import common from "./common";
import forum from "./discussion";
import game from "./game";
import mod from "./mod";
import moderation from "./moderation";
import notifications from "./notifications";
import supportUs from "./support-us";
import user from "./user";

export default {
    ...common,
    ...mod,
    ...game,
    ...notifications,
    ...user,
    ...moderation,
    ...supportUs,
    ...forum
};