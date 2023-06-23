import type { Plugin } from "rollup";

// https://github.com/so1ve/dolan-client-meme/commit/64f815fe7e689fa02b5538f6884590a8ebb682ba#diff-b1e59269cf378670f5b7be3864f70c6e29f13f26282cc9f3fbc0504bfb3ca1e5

export const ProcessVersionsNodePlugin = () =>
  ({
    name: "process-versions-node",
    transform(code) {
      if (process.env.NITRO_PRESET !== "deno") {
        return;
      }

      return {
        code: code.replace(
          /process\.versions\.node/g,
          JSON.stringify(process.versions.node),
        ),
        map: null,
      };
    },
  } as Plugin);