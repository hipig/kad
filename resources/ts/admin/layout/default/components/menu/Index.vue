<script lang="tsx">
import {defineComponent, ref, h, compile, computed} from 'vue/dist/vue.esm-bundler.js';
import {useRoute, useRouter, RouteRecordRaw} from 'vue-router';
import type {RouteMeta} from 'vue-router';
import {useAppStore} from '@admin/store';
import {listenerRouteChange} from '@admin/utils/route-listener';
import {openWindow, regexUrl} from '@admin/utils';
import useMenuTree from './useMenuTree';
import {currentMenus, MenuRecord} from "@admin/api/menu";

export default defineComponent({
    emit: ['collapse'],
    setup() {
        const appStore = useAppStore();
        const router = useRouter();
        const route = useRoute();

        const menuData = ref(appStore.appAsyncMenus);

        const collapsed = computed({
            get() {
                if (appStore.device === 'desktop') return appStore.menuCollapse;
                return false;
            },
            set(value: boolean) {
                appStore.updateSettings({menuCollapse: value});
            },
        });

        const openKeys = ref<string[]>([]);
        const selectedKey = ref<string[]>([]);

        const goto = (item: MenuRecord) => {
            // Open external link
            if (regexUrl.test(item.path)) {
                openWindow(item.path);
                selectedKey.value = [item.key as string];
                return;
            }
            // Eliminate external link side effects
            if (route.name === item.key) {
                selectedKey.value = [item.key as string];
                return;
            }
            // Trigger router change
            router.push({
                name: item.key,
            });
        };
        const findMenuOpenKeys = (name: string) => {
            const result: string[] = [];
            let isFind = false;
            const backtrack = (
                item: MenuRecord,
                keys: string[],
                target: string
            ) => {
                if (item.key === target) {
                    isFind = true;
                    result.push(...keys, item.key as string);
                    return;
                }
                if (item.children?.length) {
                    item.children.forEach((el) => {
                        backtrack(el, [...keys], target);
                    });
                }
            };
            menuData.value.forEach((el: MenuRecord) => {
                if (isFind) return; // Performance optimization
                backtrack(el, [el.key as string], name);
            });
            return result;
        };
        listenerRouteChange((newRoute) => {
            const menuOpenKeys = findMenuOpenKeys(
                newRoute.name as string
            );

            const keySet = new Set([...menuOpenKeys, ...openKeys.value]);
            openKeys.value = [...keySet];

            selectedKey.value = [
                menuOpenKeys[menuOpenKeys.length - 1],
            ];
        }, true);
        const setCollapse = (val: boolean) => {
            if (appStore.device === 'desktop')
                appStore.updateSettings({menuCollapse: val});
        };

        const renderSubMenu = () => {
            function travel(_route: RouteRecordRaw[], nodes = []) {
                if (_route) {
                    _route.forEach((element) => {
                        // This is demo, modify nodes as needed
                        const icon = element?.icon
                            ? () => h(compile(`<${element?.icon}/>`), {class: 'text-lg'})
                            : null;
                        const node =
                            element?.children && element?.children.length !== 0 ? (
                                <a-sub-menu
                                    key={element?.key}
                                    v-slots={{
                                        icon,
                                        title: () => element?.name,
                                    }}
                                >
                                    {travel(element?.children)}
                                </a-sub-menu>
                            ) : (
                                <a-menu-item
                                    key={element?.key}
                                    v-slots={{icon}}
                                    onClick={() => goto(element)}
                                >
                                    {element?.name}
                                </a-menu-item>
                            );
                        nodes.push(node as never);
                    });
                }
                return nodes;
            }

            return travel(menuData.value);
        };

        return () => (
            <a-menu
                v-model:collapsed={collapsed.value}
                v-model:open-keys={openKeys.value}
                show-collapse-button={appStore.device !== 'mobile'}
                auto-open={false}
                selected-keys={selectedKey.value}
                auto-open-selected={true}
                level-indent={34}
                style="height: 100%"
                onCollapse={setCollapse}
            >
                {renderSubMenu()}
            </a-menu>
        );
    },
});
</script>
