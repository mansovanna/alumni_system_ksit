export function useAvatar() {
  function initials(name: string): string {
    return name
      .split(" ")
      .map((n) => n[0])
      .join("")
      .toUpperCase()
      .slice(0, 2);
  }

  function color(name: string): string {
    // Create hash from name
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
      hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    const hue = hash % 360;
    return `hsl(${hue}, 85%, 45%)`;
  }

  function textToImage(name: string, size = 100): string {
    const canvas = document.createElement("canvas");
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext("2d");
    if (!ctx) return "";

    // Background
    ctx.fillStyle = color(name);
    ctx.fillRect(0, 0, size, size);

    // Text
    ctx.fillStyle = "#ffffff";
    ctx.font = `bold ${size / 2}px sans-serif`;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(initials(name), size / 2, size / 2);

    return canvas.toDataURL("image/png");
  }

  return { initials, color, textToImage };
}
