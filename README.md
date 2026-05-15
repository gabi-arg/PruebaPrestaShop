# Product Badges — Módulo PrestaShop

Módulo para gestionar etiquetas visuales (badges) sobre las imágenes de productos en PrestaShop 1.7+.

## Instalación

1. Cloná el repositorio
2. Copiá la carpeta `modules/productbadges/` dentro de `tu-prestashop/modules/`
3. En el back office ve a **Módulos > Módulos y Servicios**, buscá "Product Badges" e instalá

No requiere dependencias externas ni configuración adicional.

## Funcionalidades

- Crear y gestionar badges desde el back office (Catálogo > Product Badges)
- Cada badge tiene: texto traducible, color de fondo, color de texto, posición (superior izquierda/derecha) y estado activo/inactivo
- Asignación de badges a productos desde la ficha de producto (pestaña "Product Badges")
- Visualización en listados de categoría, búsqueda, home y ficha de producto
- Pantalla de configuración global: activar/desactivar, mostrar en listados, mostrar en ficha, máximo de badges por producto
- Soporte multilenguaje (es/en) con fallback al idioma por defecto
- Compatible con instalaciones multitienda

## Decisiones técnicas

- **Hooks de listado**: se registran tres hooks (`displayProductListReviews`, `displayAfterProductThumbs`, `displayProductBadgesListing`) para maximizar compatibilidad con distintos temas, ya que no hay un hook universal en PS 1.7 para inyectar contenido sobre la imagen en listados.
- **Ficha de producto**: se usa `displayProductAdditionalInfo` en lugar de `displayProductImages` por ser más estable entre versiones. Las badges se posicionan sobre la imagen vía CSS absoluto. Dependiendo del tema puede requerir ajuste de selectores CSS.
- **Sanitización**: todos los IDs se castean con `(int)`, los strings con `pSQL()`, y los textos en templates con `escape:'html':'UTF-8'`. La validación server-side se hace en `postProcess()` y en el ObjectModel.
- **SQL separado**: la lógica de creación/eliminación de tablas está en `sql/install.php` y `sql/uninstall.php` para mantener el módulo principal limpio.
- **Assets**: el CSS se carga solo en frontend y únicamente si el módulo está activo, evitando carga innecesaria en el back office.

## Qué quedó fuera y por qué

- **Visualización de badges en el frontend**: la creación, configuración y asignación de badges a productos funciona correctamente. No logré verificar la visualización en el frontend — los hooks están registrados y el CSS implementado, pero no pude confirmar que las badges se rendericen sobre la imagen en el tema activo. Es el punto que más tiempo me llevó y donde más limitaciones encontré al no conocer el ecosistema de temas de PrestaShop.
- **Tests unitarios**: no se incluyeron por no ser obligatorios según las instrucciones.
- **Badges por tienda en multitienda**: el módulo no rompe en multitienda y respeta el contexto activo, pero las badges no difieren por tienda. Las instrucciones indicaban que esto no era obligatorio.
- **Preview en tiempo real**: al crear una badge, la previsualización no se actualiza en vivo al cambiar los colores. Se ve el resultado solo después de guardar.

## Asunciones

- Se asume que el tema activo soporta al menos uno de los hooks de listado registrados. Si no es así, las badges no se mostrarán en listados pero sí en la ficha de producto.
- Se asume PrestaShop 1.7 con tema Classic o derivado.

## Nota personal

No tengo experiencia previa con PHP ni PrestaShop. Me apoyé en documentación oficial, 
IA y mi conocimiento de lógica y programación general para llevar adelante esta prueba.

Preferí tomarme más tiempo del estimado para entender cada decisión antes de escribir 
el código, en lugar de entregar algo que no pudiera explicar. Me resultó un desafío 
interesante y disfruté aprender algo nuevo en el proceso.