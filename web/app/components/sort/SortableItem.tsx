import { UniqueIdentifier } from "@dnd-kit/core";

export function SortableItem({ itemId }: { itemId: UniqueIdentifier }) {
  return <div className="w-full h-24 flex justify-center items-center border-2 border-dashed border-slate-100/50">{`id: ${itemId}`}</div>;
}
