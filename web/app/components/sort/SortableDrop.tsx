import { UniqueIdentifier } from "@dnd-kit/core";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { Droppable } from "./Droppable";
import { SortableProps } from "./Sortable";

export function SortableDrop({
  children,
  id,
  className,
  onClick,
}: SortableProps) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    backgroundColor: isDragging ? "green" : undefined,
  };

  return (
    <div
      className={`${className}`}
      ref={setNodeRef}
      style={style}
      {...attributes}
      {...listeners}
      onClick={onClick}
    >
      <Droppable id={id + "-menu"}>{children}</Droppable>
    </div>
  );
}
