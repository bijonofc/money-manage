import { computed, defineComponent, getCurrentInstance, h, onMounted, ref } from "vue";
import {useGettext} from "vue3-gettext";

export const AppGettextComponent = defineComponent({
    name: "TranslateBn",
    props: {
        tag: {
            type: String,
            default: "span",
        },
        translateN: {
            type: Number,
            default: null,
        },
        translatePlural: {
            type: String,
            default: null,
        },
        translateContext: {
            type: String,
            default: null,
        },
        translateParams: {
            type: Object,
            default: null,
        },
        translateComment: {
            type: String,
            default: null,
        },
    },

    setup(props, context) {
        const isPlural = props.translateN !== undefined && props.translatePlural !== undefined;

        if (!isPlural && (props.translateN || props.translatePlural)) {
            throw new Error(
                "`translate-n` and `translate-plural` attributes must be used together: " +
                (context.slots.default ? context.slots.default()[0].children : "")
            );
        }

        const root = ref(null);
        const plugin = useGettext();
        const msgid = ref(null);

        onMounted(() => {
            if (!msgid.value && root.value) {
                msgid.value = root.value.innerHTML.trim();
            }
        });

        const translatedParams = computed(() => plugin.translate_params_digits(props.translateParams));

        const translation = computed(() => {
            const translatedMsg = plugin.$gettext(
                msgid.value,
                props.translateN,
                props.translateContext,
                isPlural ? props.translatePlural : null,
                plugin.current
            );

            return plugin.interpolate(
                translatedMsg,
                translatedParams.value,
                undefined,
                getCurrentInstance() ? getCurrentInstance().parent : undefined
            );
        });

        return () => {
            if (!msgid.value) {
                // render tag wrapping the default slot content
                return h(props.tag, { ref: root }, context.slots.default ? context.slots.default() : "");
            }
            // render tag wrapping translated HTML (innerHTML)
            return h(props.tag, { ref: root, innerHTML: translation.value });
        };
    }
});

export default AppGettextComponent;
