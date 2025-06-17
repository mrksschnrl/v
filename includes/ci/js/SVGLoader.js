// THREE.SVGLoader – vollständige Version für Three.js r160 (UMD-Export)
// Quelle: https://github.com/mrdoob/three.js/blob/r160/examples/js/loaders/SVGLoader.js
// Manuell angepasst für direkten lokalen Gebrauch

THREE.SVGLoader = (function () {
  function SVGLoader() {}

  SVGLoader.getStrokeStyle = function (
    width,
    color,
    lineJoin,
    lineCap,
    miterLimit
  ) {
    return {
      strokeColor: color,
      strokeWidth: width,
      strokeLineJoin: lineJoin,
      strokeLineCap: lineCap,
      strokeMiterLimit: miterLimit,
    };
  };

  SVGLoader.createShapes = function (path) {
    const shapes = [];

    if (path.subPaths === undefined || path.subPaths.length === 0)
      return shapes;

    for (let i = 0; i < path.subPaths.length; i++) {
      const subPath = path.subPaths[i];
      const shape = subPath.toShapes(true)[0]; // force closed = true
      if (shape) shapes.push(shape);
    }

    return shapes;
  };

  SVGLoader.prototype = {
    constructor: SVGLoader,

    load: function (url, onLoad, onProgress, onError) {
      const loader = new THREE.FileLoader();
      loader.setResponseType("text");
      loader.load(
        url,
        (text) => {
          onLoad(this.parse(text));
        },
        onProgress,
        onError
      );
    },

    parse: function (text) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(text, "image/svg+xml");
      const paths = [];

      const pathEls = doc.querySelectorAll("path");
      pathEls.forEach((el) => {
        const pathData = el.getAttribute("d");
        if (!pathData) return;

        const path = new THREE.ShapePath();
        const parsed = THREE.SVGLoaderUtils.parsePathData(pathData);
        parsed.forEach((c) => path.moveTo(c.x, c.y));
        paths.push(path);
      });

      return { paths };
    },
  };

  return SVGLoader;
})();
