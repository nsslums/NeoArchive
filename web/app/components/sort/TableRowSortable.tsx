import { UniqueIdentifier } from "@dnd-kit/core";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { TableRow } from "../ui/table";

export type SortableProps = {
  children: React.ReactNode;
  id: UniqueIdentifier;
  className?: string;
  onClick?: () => void;
};

export function TableRowSortable({ children, id, className, onClick }: SortableProps) {
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
    <TableRow
      className={`${className}`}
      ref={setNodeRef}
      style={style}
      {...attributes}
      {...listeners}
      onClick={onClick}
    >
      {children}
    </TableRow>
  );
}
